<?php

namespace Hui\Xproject\Listeners;

use Hui\Xproject\Events\PaymentSaved;
use Hui\Xproject\Exceptions\XprojectException;
use Hui\Xproject\Services\TodoManager\TodoManager;
use Illuminate\Support\Facades\DB;

class PaymentSavedListener{
  public function handle(PaymentSaved $paymentSaved):void{
    /**
     * @var TodoManager $todoManager
     */
    $todoManager=app(TodoManager::class);

    DB::statement('LOCK todos IN EXCLUSIVE MODE');

    $payment=$paymentSaved->payment;
    if($payment->successful()&&$payment->status!==$payment->getOriginal('status')){
      $todo=$payment->todo;

      if($todoManager->canBePaid($todo)){
        $todoManager->markAsPaid($todo);
        $todo->save();

        logger(sprintf('To-do %u paid succesfully (payment %u)',
          $todo->id,
          $payment->id));
      }
      else
        throw new XprojectException(sprintf('Cannot mark to-do %u as paid',$todo->id));
    }
  }
}
