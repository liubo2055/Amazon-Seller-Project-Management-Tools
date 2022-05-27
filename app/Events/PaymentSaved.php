<?php

namespace Hui\Xproject\Events;

use Hui\Xproject\Entities\Payment;
use Illuminate\Queue\SerializesModels;

class PaymentSaved{
  use SerializesModels;

  public function __construct(Payment $payment){
    $this->payment=$payment;
  }

  /**
   * @var Payment
   */
  public $payment;
}
