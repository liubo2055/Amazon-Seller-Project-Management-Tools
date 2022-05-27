<?php

namespace Hui\Xproject\Http\Controllers;

use Hui\AppsIoLaravel\Services\AppsIoHelper;
use Hui\Xproject\Entities\Payment;
use Hui\Xproject\Entities\Project;
use Hui\Xproject\Entities\Todo;
use Hui\Xproject\Entities\User;
use Hui\Xproject\Http\Controllers\Traits\ListTrait;
use Hui\Xproject\Services\MarketplaceInformation\MarketplaceInformation;
use Hui\Xproject\Services\PaymentManager\PaymentManager;
use Hui\Xproject\Services\ProjectManager\ProjectManager;
use Hui\Xproject\Services\TodoManager\TodoManager;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TodosController extends Controller{
  use ListTrait;

  const CODE_HASH_SALT=0x3f882046;

  public function __construct(MarketplaceInformation $marketplaceInformation,TodoManager $todoManager,PaymentManager $paymentManager,ProjectManager $projectManager){
    $this->marketplaceInformation=$marketplaceInformation;
    $this->todoManager=$todoManager;
    $this->paymentManager=$paymentManager;
    $this->projectManager=$projectManager;
  }

  public function index():View{
    return view('todos');
  }

  public function list(Request $request):array{
    $orderCallback=function(string $order):string{
      switch($order){
        case 'asin':
          return 'product_asin';
        case 'price':
          return 'product_price';
        case 'title':
          return 'product_title';
        case 'seller':
          return 'seller_name';
        case 'shipping':
          return 'fba';
        case 'created':
          return 'created_at';
        default:
          return snake_case($order);
      }
    };

    $rowsCallback=function(string $orderColumn,bool $ascOrder,int $page,int $size,array $filters):array{
      $user=user();

      $todos=Todo
        ::orderBy($orderColumn,$ascOrder?'ASC':'DESC')
        ->offset(($page-1)*$size)
        ->limit($size)
        ->with([
          'projects',
          'projectsWithTrashed',
          'user',
          'payments'
        ]);
      $this->todoManager->restrictQueryForUser($todos,$user);
      $this->filter($todos,$filters);
      $todos=$todos->get();

      $accountsConfig=config('accounts');
      $freelancerUser=$user->isFreelancer();
      $managerUser=$user->isManagerOrAdmin();

      $rows=[];
      /**
       * @var Todo $todo
       */
      foreach($todos as $todo){
        $productUrl=$todo->product_url;
        if($freelancerUser&&isset($accountsConfig[$todo->marketplace])){
          if($todo->keywords)
            $keyword=array_random($todo->keywords);
          else
            $keyword='';
          $productUrl=$this->marketplaceInformation->addTag($productUrl,$accountsConfig[$todo->marketplace]['subscriptionId'],
            $accountsConfig[$todo->marketplace]['tag'],$keyword);
        }

        if($todo->projects->isNotEmpty()){
          $progress=$this->todoManager->progress($todo);
          $progress=[
            'completed'=>$progress[Project::STATUS_COMPLETED],
            'unassigned'=>$progress[Project::STATUS_UNASSIGNED],
            'others'=>$progress[Project::STATUS_ACCEPTED]
              +$progress[Project::STATUS_WAITING_DESCRIPTION]
              +$progress[Project::STATUS_WAITING_URL]
              +$progress[Project::STATUS_WAITING_CONFIRMATION],
            'failed'=>$progress[Project::STATUS_FAILED]+$progress[Project::STATUS_DELETED]
          ];
        }
        else
          $progress=null;

        $seller=[
          'id'=>$todo->seller_id,
          'storefrontName'=>$todo->storefront_name,
          'storefrontUrl'=>$todo->storefront_url
        ];
        if(!$freelancerUser)
          $seller['name']=$todo->seller_name;
        if($managerUser)
          $seller['phone']=$todo->user->isEmployer()?$todo->user->phone:null;

        $rows[]=[
          'code'=>[
            'code'=>$todo->code,
            'asin'=>$todo->product_asin,
            'url'=>$productUrl,
            'private'=>$todo->private
          ],
          'price'=>[
            'price'=>sprintf('%s %s',
              $this->marketplaceInformation->currency($todo->marketplace),
              number_format($todo->product_price,2)),
            'shipping'=>$todo->fba?_ix('FBA','To-Dos'):_ix('FBM','To-Dos')
          ],
          'title'=>[
            'title'=>$todo->product_title,
            'description'=>$todo->product_description
          ],
          'seller'=>$seller,
          'notesKeywords'=>[
            'notes'=>$todo->notes,
            'keywords'=>$todo->keywords
          ],
          'status'=>[
            'status'=>$todo->statusName(),
            'limitReached'=>$freelancerUser&&$this->todoManager->limitReachedForTodo($todo)
          ],
          'progress'=>$progress,
          'created'=>$todo->created_at->format('Y-m-d H:i:s'),
          'operations'=>$this->operations($todo),
          'meta'=>[
            'id'=>$todo->id
          ]
        ];
      }

      return $rows;
    };

    $itemsCallback=function(array $filters):int{
      $count=$this->todoManager->restrictQueryForUser(Todo::query(),user());
      $this->filter($count,$filters);

      return $count->count();
    };

    return $this->makeListResponse($request,$orderCallback,$rowsCallback,$itemsCallback);
  }

  public function load(Todo $todo):array{
    if(!$todo->exists)
      $this->todoManager->initialize($todo,user());

    return $this->doLoad($todo,user(),false);
  }

  public function newPrivate():array{
    $todo=new Todo;
    $this->todoManager->initialize($todo,user());
    $todo->private=true;

    return $this->doLoad($todo,user(),false);
  }

  public function clone(Todo $todo):array{
    return $this->doLoad($this->todoManager->clone($todo,user()),user(),true);
  }

  public function save(Todo $todo,Request $request):JsonResponse{
    return $this->doSave($todo,$request);
  }

  public function createPrivate(Request $request):JsonResponse{
    $todo=new Todo;
    $todo->private=true;

    return $this->doSave($todo,$request);
  }

  public function guess(Request $request):array{
    $input=$request->input('input');

    if($input==='product'){
      [
        $asin,
        $marketplace
      ]=$this->marketplaceInformation->guessAsinAndMarketplace((string)$request->input('productUrl'));

      return compact(
        'asin',
        'marketplace'
      );
    }
    else if($input==='store'){
      [
        $sellerId,
        $marketplace
      ]=$this->marketplaceInformation->guessMerchantAndMarketplace((string)$request->input('storefrontUrl'));

      return compact(
        'sellerId',
        'marketplace'
      );
    }
    else
      throw new HttpException(400,'Wrong input type');
  }

  public function delete(Todo $todo):void{
    $todo->delete();
  }

  public function price(Request $request):array{
    $projects=(int)$request->input('projects',0);
    $productPrice=(float)$request->input('productPrice',0);
    $marketplace=$request->input('marketplace');

    if($projects&&$productPrice&&$marketplace){
      $priceBreakdown=[];
      $price=$this->paymentManager->priceForTodo($projects,$productPrice,$this->marketplaceInformation->currency($marketplace),$priceBreakdown);
    }
    else{
      $price=null;
      $priceBreakdown=null;
    }

    return compact(
      'price',
      'priceBreakdown'
    );
  }

  public function pay(Todo $todo):array{
    $user=user();

    DB::beginTransaction();
    DB::statement('LOCK payments IN EXCLUSIVE MODE');

    $payments=$todo->payments->sortByDesc('created_at');
    if($payments->isEmpty())
      throw new HttpException(400,'No available payments');
    /**
     * @var Payment $payment
     */
    foreach($payments as $payment)
      if($payment->successful()){
        logger()->error(sprintf('Successful payment %u for to-do %u',
          $payment->id,
          $todo->id));
        throw new HttpException(400,'There is already a successful payment');
      }

    $payment=$this->paymentManager->newPaymentFromExisting($payments->first());

    if(!$this->paymentManager->testPaymentEnabled())
      $url=$this->paymentManager->requestPayment($payment,$user,route('todosCompletePayment'),route('todosNotifyPayment'));
    else{
      $url=null;

      $this->paymentManager->completePaymentForTesting($payment);
    }

    $payment->save();

    DB::commit();

    return compact('url');
  }

  public function completePayment(Request $request):RedirectResponse{
    DB::beginTransaction();

    $payload=$request->all();

    $payment=$this->paymentManager->findPayment($payload);
    logger(sprintf('Completing payment %u (sync)',$payment->id));

    if($this->paymentManager->canBeCompleted($payment)){
      $this->paymentManager->completePayment($payment,$payload);
      logger(sprintf('Payment %u completed: status %s',
        $payment->id,
        $payment->status));

      if($payment->error)
        logger()->error(sprintf('Payment %u error: %s',
          $payment->id,
          $payment->error));

      $payment->save();
    }
    else
      logger(sprintf('Payment %u cannot be completed',$payment->id));

    DB::commit();

    return redirect()->route('todos');
  }

  public function notifyPayment(Request $request){
    DB::beginTransaction();

    $payload=$request->all();

    // User is not available here
    $payment=$this->paymentManager->findPayment($payload);
    logger(sprintf('Completing payment %u (async)',$payment->id));

    $success=true;

    if($this->paymentManager->canBeCompleted($payment)){
      $this->paymentManager->completePayment($payment,$payload);
      logger(sprintf('Payment %u completed: status %s',
        $payment->id,
        $payment->status));

      if($payment->error){
        logger()->error(sprintf('Payment %u error: %s',
          $payment->id,
          $payment->error));
        $success=false;
      }

      $payment->save();
    }
    else
      logger(sprintf('Payment %u cannot be completed',$payment->id));

    DB::commit();

    if($payment->huistore_gateway)
      return $this->paymentManager->huistoreAckResponse();
    else
      return $success?'success':'fail';
  }

  public function confirm(Todo $todo):void{
    $this->todoManager->confirm($todo);
    $todo->save();
  }

  public function payments(Todo $todo):array{
    $admin=user()->isAdmin();
    $payments=$todo->payments
      ->sortByDesc('created_at')
      ->values()
      ->map(function(Payment $payment) use ($admin):array{
        $data=$payment->toArray()+[
            'status'=>$payment->statusName(),
            'date'=>$payment->created_at->format('Y-m-d H:i:s')
          ];
        if($admin)
          $data+=[
            'response'=>nl2br(print_r($payment->response,true))
          ];

        $data=arrayCamelCase($data);

        return $data;
      });

    return compact('payments');
  }

  private function doSave(Todo $todo,Request $request):JsonResponse{
    $user=user();

    $ignoredFields=$this->todoManager->ignoredFields($todo,$user);
    $ignoredFields=array_map('camel_case',$ignoredFields);

    $rules=[
      'code'=>[
        'required',
        'max:20',
        sprintf('unique:todos,code,%u',$todo->id),
        sprintf('regex:%s',$this->todoManager->codeRegexp())
      ],
      'fba'=>'required|boolean',
      'productUrl'=>'required|url|max:255',
      'productAsin'=>'required|max:10',
      'marketplace'=>sprintf('required|in:%s',implode(',',$this->marketplaceInformation->marketplaceCodes())),
      'productPrice'=>'required|numeric|min:0.01',
      'productTitle'=>'nullable|max:255',
      'productDescription'=>'nullable|max:255',
      'sellerName'=>'nullable|max:255',
      'storefrontUrl'=>sprintf('%s|url|max:255',$this->todoManager->mustProvideStorefrontUrl($user)?'required':'nullable'),
      'storefrontName'=>'required|max:255',
      'sellerId'=>'nullable|max:20',
      'dailyLimit'=>'required|integer|min:1'
    ];
    if(!$todo->exists)
      $rules+=[
        'projects'=>'required|integer|min:1|max:100',
        'projectTitleDescription'=>'required|boolean'
      ];
    foreach($ignoredFields as $ignoredField)
      unset($rules[$ignoredField]);
    $this->validate($request,$rules);

    if(!in_array('code',$ignoredFields)){
      $todo->code=$request->input('code');

      if(in_array('code',$this->todoManager->disabledFields($todo,$user))){
        $hash=$request->input('codeHash');

        if(!$this->checkCodeHash($todo->code,$hash)){
          logger()->error(sprintf('Code hash mismatch: received %s (code %s)',
            $hash,
            $todo->code));
          throw new HttpException(400,'Code hash mismatch');
        }
      }
    }

    if(!in_array('fba',$ignoredFields))
      $todo->fba=(bool)$request->input('fba');
    if(!in_array('keywords',$ignoredFields))
      $todo->keywords=(array)$request->input('keywords');
    if(!in_array('productUrl',$ignoredFields))
      $todo->product_url=$request->input('productUrl');
    if(!in_array('productAsin',$ignoredFields))
      $todo->product_asin=$request->input('productAsin');
    if(!in_array('marketplace',$ignoredFields))
      $todo->marketplace=$request->input('marketplace');
    if(!in_array('productPrice',$ignoredFields))
      $todo->product_price=$request->input('productPrice');
    if(!in_array('productTitle',$ignoredFields))
      $todo->product_title=$request->input('productTitle');
    if(!in_array('productDescription',$ignoredFields))
      $todo->product_description=$request->input('productDescription');
    if(!in_array('sellerName',$ignoredFields))
      $todo->seller_name=$request->input('sellerName');

    if(!in_array('storefrontUrl',$ignoredFields)){
      $todo->storefront_url=$request->input('storefrontUrl');

      if($user->isEmployer()){
        $found=false;

        foreach($user->storefronts as $storefront)
          if($storefront['url']===$todo->storefront_url){
            $found=true;
            break;
          }

        if(!$found){
          $errors=[
            'storefrontUrl'=>[_ix('This URL does not belong to your storefronts.','To-Dos')]
          ];

          return response()->json(compact('errors'),400);
        }
      }
    }

    if(!in_array('storefrontName',$ignoredFields)){
      $todo->storefront_name=$request->input('storefrontName');

      if($user->isEmployer()){
        $found=false;

        foreach($user->storefronts as $storefront)
          if($storefront['url']===$todo->storefront_url){
            $found=in_array($todo->storefront_name,$storefront['names']);
            break;
          }

        if(!$found){
          $errors=[
            'storefrontName'=>[_ix('This name does not belong to your storefronts.','To-Dos')]
          ];

          return response()->json(compact('errors'),400);
        }
      }
    }

    if(!in_array('sellerId',$ignoredFields))
      $todo->seller_id=$request->input('sellerId');
    if(!in_array('notes',$ignoredFields))
      $todo->notes=$request->input('notes');
    if(!in_array('dailyLimit',$ignoredFields))
      $todo->daily_limit=(int)$request->input('dailyLimit');
    if(!in_array('projectTitleDescription',$ignoredFields))
      $todo->project_title_description=(bool)$request->input('projectTitleDescription');

    DB::beginTransaction();

    if($this->todoManager->mustGenerateCode($todo,$user)){
      // Prevent SELECTs and any kind of updates to the to-dos table
      DB::statement('LOCK todos IN ACCESS EXCLUSIVE MODE');

      $todo->code=$this->todoManager->generateCode();
    }

    $url=null;

    if(!$todo->exists){
      $todo->user_id=$user->id;

      if($todo->marketplace){
        $errors=[];

        $productErrors=[];

        [
          $asin,
          $marketplace
        ]=$this->marketplaceInformation->guessAsinAndMarketplace($todo->product_url);
        if($asin!==$todo->product_asin)
          $productErrors[]=_ix('This is not a valid URL for the product ASIN.','To-Dos');
        if($marketplace!==$todo->marketplace)
          $productErrors[]=_ix('This is not a valid URL for the marketplace.','To-Dos');

        if($productErrors)
          $errors['productUrl']=$productErrors;

        if($todo->storefront_url){
          $storeErrors=[];

          [
            $sellerId,
            $marketplace
          ]=$this->marketplaceInformation->guessMerchantAndMarketplace($todo->storefront_url);

          if($todo->seller_id&&$sellerId!==$todo->seller_id)
            $storeErrors[]=_ix('This is not a valid URL for the seller ID.','To-Dos');
          if($marketplace!==$todo->marketplace)
            $storeErrors[]=_ix('This is not a valid URL for the marketplace.','To-Dos');

          if($storeErrors)
            $errors['storefrontUrl']=$storeErrors;
        }

        if($errors)
          return response()->json(compact('errors'),400);
      }

      // Zero for existing to-dos
      $projectsCount=(int)$request->input('projects',0);

      $paymentRequired=$this->todoManager->mustBePaidBy($todo,$user);
      if($paymentRequired){
        $expectedPrice=(float)$request->input('price');
        $price=$this->paymentManager->priceForTodo($projectsCount,$todo->product_price,$this->marketplaceInformation->currency($todo->marketplace));

        if($expectedPrice!==$price){
          logger()->error(sprintf('To-do price mismatch: expected %.02f, calculated %.02f (%u projects)',
            $expectedPrice,
            $price,
            $projectsCount));
          throw new HttpException(400,'To-do price mismatch');
        }

        $todo->employer_status=Todo::STATUS_EMPLOYER_UNPAID;
      }

      $todo->save();

      $populateProjectTitleDescription=!$todo->project_title_description;
      foreach($this->projectManager->createProjectsForNewTodo($todo,$projectsCount,$populateProjectTitleDescription) as $project)
        $project->save();

      if($paymentRequired){
        $huistoreGateway=!empty($request->input('huistoreGateway'));
        if($huistoreGateway&&!$this->paymentManager->canUseHuistoreGateway())
          throw new HttpException(400,'Operation not allowed');

        /** @noinspection PhpUndefinedVariableInspection */
        $payment=$this->paymentManager->newPayment($todo,$price,$huistoreGateway);

        if(!$this->paymentManager->testPaymentEnabled())
          $url=$this->paymentManager->requestPayment($payment,$user,route('todosCompletePayment'),route('todosNotifyPayment'));
        else
          $this->paymentManager->completePaymentForTesting($payment);

        $payment->save();
      }
    }
    else{
      // Precondition: payment is never required to update an existing to-do

      DB::statement('LOCK projects IN ACCESS EXCLUSIVE MODE');

      $this->projectManager->updateProjectsFromTodo($todo);

      $todo->push();
    }

    DB::commit();

    return response()->json(compact('url'));
  }

  private function doLoad(Todo $todo,User $user,bool $cloning):array{
    if(!$cloning)
      $projects=$todo->exists?$todo->projects->count():1;
    else
      $projects=1;

    $originalSellerName=null;
    if($todo->exists){
      $userName=$todo->user->name;
      if($userName!==$todo->seller_name)
        $originalSellerName=$userName;
    }

    $disabledFields=$this->todoManager->disabledFields($todo,$user);
    $disabledFields=array_map('camel_case',$disabledFields);
    if($todo->exists)
      $disabledFields[]='projects';

    $huistoreGatewayFunds=null;
    if($paymentRequired=$this->todoManager->mustBePaidBy($todo,$user)){
      /**
       * @var AppsIoHelper $appsIoHelper
       */
      $appsIoHelper=app(AppsIoHelper::class);
      if($appsIoHelper->remoteUser())
        $huistoreGatewayFunds=$appsIoHelper->fetchRemoteUserBalance();
    }

    if($todo->exists)
      $saveUrl=route('todosSave',$todo->id);
    else if($todo->private)
      $saveUrl=route('todosSaveNewPrivate');
    else
      $saveUrl=route('todosSaveNew');

    $data=[
      'todo'=>$todo->toArray()+compact(
          'projects',
          'originalSellerName'
        ),
      'disabledFields'=>$disabledFields,
      'paymentRequired'=>$paymentRequired,
      'huistoreGatewayFunds'=>$huistoreGatewayFunds,
      'saveUrl'=>$saveUrl,
      'storefrontUrlRequired'=>$this->todoManager->mustProvideStorefrontUrl($user),
      'hideCode'=>$this->todoManager->mustGenerateCode($todo,$user)
    ];
    if($paymentRequired)
      $data['priceUrl']=route('todosPrice');
    if($user->isEmployer())
      $data['storefronts']=$this->storefronts($user->storefronts??[]);
    if(in_array('code',$disabledFields)&&!$this->todoManager->mustGenerateCode($todo,$user))
      $data['codeHash']=$this->codeHash($todo->code);

    $data=arrayCamelCase($data);

    return $data;
  }

  private function filter(Builder $query,array $filters):void{
    foreach($filters as $filter=>$value)
      if($value!==null)
        switch($filter){
          case 'statuses':
            $this->todoManager->filterByStatuses($query,$value);
            break;
          case 'asinCode':
            $search=sprintf('%%%s%%',mb_strtolower($value));
            $query->where(function(Builder $query) use ($search):void{
              $query
                ->where(DB::raw('lower(product_asin)'),'LIKE',$search)
                ->orWhere(DB::raw('lower(code)'),'LIKE',$search);
            });
            break;
          case 'sellerIdStorefrontName':
            $search=sprintf('%%%s%%',mb_strtolower($value));
            $query->where(function(Builder $query) use ($search):void{
              $query
                ->where(DB::raw('lower(seller_id)'),'LIKE',$search)
                ->orWhere(DB::raw('lower(storefront_name)'),'LIKE',$search);
            });
            break;
          case 'sellerName':
            $search=sprintf('%%%s%%',mb_strtolower($value));
            $query->where(DB::raw('lower(seller_name)'),'LIKE',$search);
            break;
          case 'freelancer':
            $this->todoManager->filterByFreelancerName($query,$value);
            break;
          case 'date':
            if($value[0])
              $query->where('created_at','>=',$value[0]);
            if($value[1])
              $query->where('created_at','<=',sprintf('%s 23:59:59',$value[1]));
            break;
          case 'private':
            $query->where('private',$value==='yes');
            break;
          default:
            throw new HttpException(400,'Wrong filter');
        }
  }

  private function operations(Todo $todo):array{
    $user=user();

    $operations=[];
    if($user->can('viewProjects',$todo))
      $operations['projects']=[
        'url'=>route('projectsForTodo',$todo->id)
      ];
    if($user->can('edit',$todo))
      $operations['edit']=[
        'url'=>route('todosLoad',$todo->id)
      ];
    if($user->can('clone',$todo))
      $operations['clone']=[
        'url'=>route('todosClone',$todo->id)
      ];
    if($user->can('viewPayments',$todo)&&$todo->payments->isNotEmpty())
      $operations['payments']=[
        'url'=>route('todosPayments',$todo->id)
      ];
    if($user->can('delete',$todo))
      $operations['delete']=[
        'url'=>route('todosDelete',$todo->id)
      ];
    if($user->can('pay',$todo))
      $operations['pay']=[
        'url'=>route('todosPay',$todo->id)
      ];
    if($user->can('confirm',$todo))
      $operations['confirm']=[
        'url'=>route('todosConfirm',$todo->id)
      ];

    return $operations;
  }

  private function storefronts(array $storefronts):array{
    return array_map(function(array $storefront):array{
      [
        $sellerId,
        $marketplace
      ]=$this->marketplaceInformation->guessMerchantAndMarketplace($storefront['url']);

      $storefront['marketplaceSellerId']=sprintf('%s, %s',
        $this->marketplaceInformation->name($marketplace),
        $sellerId);

      return $storefront;
    },$storefronts);
  }

  private function codeHash(string $code):string{
    return bcrypt(sprintf('%s%x',
      $code,
      static::CODE_HASH_SALT));
  }

  private function checkCodeHash(string $code,string $hash):bool{
    $value=sprintf('%s%x',
      $code,
      static::CODE_HASH_SALT);

    /**
     * @var Hasher $hasher
     */
    $hasher=app('hash');

    return $hasher->check($value,$hash);
  }

  private $marketplaceInformation;

  private $todoManager;

  private $paymentManager;

  private $projectManager;
}
