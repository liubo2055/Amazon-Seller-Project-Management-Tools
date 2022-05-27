<?php

namespace Hui\Xproject\Entities;

use Carbon\Carbon;
use Hui\Xproject\Events\PaymentSaved;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int     $id
 * @property int          $todo_id
 * @property string       $code
 * @property float        $amount
 * @property float|null   $amount_due
 * @property float|null   $amount_paid
 * @property bool         $huistore_gateway
 * @property string       $status
 * @property string|null  $buyer_email
 * @property string|null  $buyer_id
 * @property string|null  $notify_id
 * @property mixed[]|null $response
 * @property string|null  $error
 * @property Carbon       $created_at
 * @property Carbon       $updated_at
 * @property Carbon|null  $deleted_at
 * @property Todo         $todo
 */
class Payment extends Model{
  use SoftDeletes;

  const STATUSES=[
    self::STATUS_PENDING,
    self::STATUS_SUCCESSFUL,
    self::STATUS_FAILED
  ];
  const STATUS_FAILED='failed';
  const STATUS_PENDING='pending';
  const STATUS_SUCCESSFUL='successful';

  public static function statusNames():array{
    return [
      static::STATUS_PENDING=>_ix('Pending','Payment'),
      static::STATUS_SUCCESSFUL=>_ix('Successful','Payment'),
      static::STATUS_FAILED=>_ix('Failed','Payment')
    ];
  }

  public static function findByCodeAndLock(string $code):Payment{
    return static
      ::where(compact('code'))
      ->lockForUpdate()
      ->firstOrFail();
  }

  public function todo():BelongsTo{
    return $this->belongsTo(Todo::class);
  }

  public function successful():bool{
    return $this->status===static::STATUS_SUCCESSFUL;
  }

  public function statusName():string{
    return static::statusNames()[$this->status];
  }

  protected $dates=['deleted_at'];

  protected $casts=[
    'response'=>'json'
  ];

  protected $visible=[
    'code',
    'amount',
    'amount_due',
    'amount_paid',
    'buyer_email',
    'buyer_id',
    'notify_id',
    'error'
  ];

  protected $dispatchesEvents=[
    'saved'=>PaymentSaved::class
  ];
}
