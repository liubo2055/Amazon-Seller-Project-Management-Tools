<?php

namespace Hui\Xproject\Services\PaymentManager;

use Exception;
use Hui\AppsIoLaravel\Services\AppsIoHelper;
use Hui\AppsIoLaravel\Services\HuistoreGatewayResponse;
use Hui\Xproject\Entities\Payment;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;
use Omnipay\Alipay\LegacyExpressGateway;
use Omnipay\Alipay\Responses\AopTradeAppPayResponse;
use Omnipay\Common\Exception\OmnipayException;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Omnipay;

class PaymentManager{
  public function __construct(AppsIoHelper $appsIoHelper){
    $this->appsIoHelper=$appsIoHelper;
  }

  public function newPayment(Todo $todo,float $amount,bool $huistoreGateway):Payment{
    $payment=new Payment;
    $payment->todo()
      ->associate($todo);
    $payment->code=$this->paymentCode();
    $payment->amount=$amount;
    $payment->amount_due=$amount;
    $payment->huistore_gateway=$huistoreGateway;
    $payment->status=Payment::STATUS_PENDING;

    return $payment;
  }

  public function requestPayment(Payment $payment,User $user,string $completeUrl,string $notifyUrl):string{
    if($payment->huistore_gateway)
      return $this->requestHuistorePayment($payment,$user,$completeUrl,$notifyUrl);
    else
      return $this->requestAlipayPayment($payment,$user,$completeUrl,$notifyUrl);
  }

  public function findPayment(array $payload):Payment{
    if($response=$this->appsIoHelper->huistoreGatewayResponse($payload))
      $code=$response->code;
    else if(!empty($payload['out_trade_no']))
      $code=$payload['out_trade_no'];
    else{
      logger()->error(print_r($payload,true));
      throw new PaymentManagerException('Code parameter not found');
    }

    return Payment::findByCodeAndLock($code);
  }

  public function canBeCompleted(Payment $payment):bool{
    return $payment->status===Payment::STATUS_PENDING;
  }

  public function completePayment(Payment $payment,array $payload){
    if($response=$this->appsIoHelper->huistoreGatewayResponse($payload))
      $this->completeHuistorePayment($payment,$response);
    else
      $this->completeAlipayPayment($payment,$payload);
  }

  public function testPaymentEnabled():bool{
    $testPayment=config('payments.test_payments');
    if($testPayment&&!app()->environment('local'))
      throw new PaymentManagerException('Test payments cannot be run outside the local environment');

    return (bool)$testPayment;
  }

  public function priceForTodo(int $projects,float $productPrice,string $currency,?array &$breakdown=null):float{
    $fixed=config('payments.fees.todos');
    if($fixed===null)
      throw new PaymentManagerException('Fees not set');

    $variable=$this->exchangeRate($currency);

    if($breakdown!==null)
      $breakdown=compact(
        'projects',
        'variable',
        'productPrice',
        'fixed'
      );

    // Alipay doesn't work with more than two decimal places
    return round($projects*($variable*$productPrice+$fixed),2);
  }

  public function completePaymentForTesting(Payment $payment):void{
    $payment->amount_paid=$payment->amount_due;
    $payment->status=Payment::STATUS_SUCCESSFUL;
  }

  public function newPaymentFromExisting(Payment $existingPayment):Payment{
    return $this->newPayment($existingPayment->todo,$existingPayment->amount,$existingPayment->huistore_gateway);
  }

  public function canUseHuistoreGateway():bool{
    return (bool)$this->appsIoHelper->remoteUser();
  }

  public function huistoreAckResponse():array{
    return $this->appsIoHelper->paymentNotifyAckResponse();
  }

  private function requestHuistorePayment(Payment $payment,User $user,string $completeUrl,string $notifyUrl):string{
    return $this->appsIoHelper->requestPayment($payment->amount,$this->paymentSubject($user),$this->details($payment),$payment->code,$completeUrl,$notifyUrl);
  }

  private function requestAlipayPayment(Payment $payment,User $user,string $returnUrl,string $notifyUrl):string{
    $params=[
      'out_trade_no'=>$payment->code,
      'subject'=>$this->paymentSubject($user),
      'total_fee'=>$payment->amount_due
    ];
    $response=$this->alipayGateway($returnUrl,$notifyUrl)
      ->purchase($params)
      ->send();

    /**
     * @var RedirectResponseInterface $response
     */
    return $response->getRedirectUrl();
  }

  private function completeHuistorePayment(Payment $payment,HuistoreGatewayResponse $response):void{
    // Unlike Alipay, we do not send any acknowledgment here
    // Instead, at least one notify request should be sent by the server

    if($response->confirm)
      $payment->status=Payment::STATUS_SUCCESSFUL;
    else{
      $payment->status=Payment::STATUS_FAILED;
      $payment->error=_ix('Huistore payment cancelled','Payment manager');
    }

    $payment->response=$response->toArray();
  }

  private function completeAlipayPayment(Payment $payment,array $payload):void{
    try{
      /**
       * @var AopTradeAppPayResponse $response
       */
      $response=$this->alipayGateway(null,null)
        ->completePurchase()
        ->setParams($payload)
        ->send();

      if($response->isSuccessful())
        $payment->status=Payment::STATUS_SUCCESSFUL;
      else{
        $payment->status=Payment::STATUS_FAILED;
        $payment->error=$response->getMessage();
      }

      $data=$response->data();

      $payment->response=$data;
      $payment->buyer_email=$data['buyer_email']??'';
      $payment->buyer_id=$data['buyer_id']??'';
      $payment->notify_id=$data['notify_id']??'';

      $user=$payment->todo->user;
      if($user->apps_io_user_id)
        try{
          $this->appsIoHelper->registerAlipayPayment($payment->amount,$this->paymentSubject($user),$this->details($payment),$payment->code,
            user()?null:$user->apps_io_user_id);

          logger('Alipay payment registered');
        }
        catch(Exception $e){
          logger()->error(sprintf('Exception while registering the Alipay payment %s: %s (%s:%u)',
            $payment,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()));
        }
      else
        logger('Alipay payment not registered (user is not remote)');
    }
    catch(OmnipayException $e){
      logger()->error(sprintf('Exception while completing payment %u (code %s): %s (%s:%u)',
        $payment->id,
        $payment->code,
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()));

      $payment->status=Payment::STATUS_FAILED;
      $payment->error=sprintf(_ix('Internal error: %s','Payment manager'),$e->getMessage());
    }
  }

  private function paymentCode():string{
    return str_random(64);
  }

  private function alipayGateway(?string $returnUrl,?string $notifyUrl):LegacyExpressGateway{
    /**
     * @var LegacyExpressGateway $gateway
     */
    $gateway=Omnipay::create('Alipay_LegacyExpress');
    $gateway->setSellerEmail(config('payments.alipay.seller_email'));
    $gateway->setPartner(config('payments.alipay.partner'));
    $gateway->setKey(config('payments.alipay.key'));
    // Not always required
    if($returnUrl)
      $gateway->setReturnUrl($returnUrl);
    if($notifyUrl)
      $gateway->setNotifyUrl($notifyUrl);

    return $gateway;
  }

  private function paymentSubject(User $user):string{
    return _ix(sprintf('X-project purchase from %s',$user->name??$user->id),'Payment manager');
  }

  private function exchangeRate(string $currency):float{
    $exchangeRates=config('payments.exchangeRates');
    if(empty($exchangeRates[$currency]))
      throw new PaymentManagerException(sprintf('Exchange rate not available for %s',$currency));

    return $exchangeRates[$currency];
  }

  private function details(Payment $payment):array{
    return [
      'id'=>$payment->todo_id,
      'item'=>$payment->todo->product_asin,
      'buyerEmail'=>$payment->buyer_email,
      'tradeNumber'=>$payment->response['trade_no']??null
    ];
  }

  private $appsIoHelper;
}
