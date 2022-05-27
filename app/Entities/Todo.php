<?php

namespace Hui\Xproject\Entities;

use Carbon\Carbon;
use Hui\Xproject\Services\TodoManager\TodoManager;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int             $id
 * @property int                  $user_id
 * @property string               $code
 * @property bool                 $fba
 * @property string               $product_url
 * @property string               $product_asin
 * @property string               $marketplace
 * @property float                $product_price
 * @property string|null          $product_title
 * @property string|null          $product_description
 * @property string|null          $seller_name
 * @property string|null          $storefront_url
 * @property string               $storefront_name
 * @property string|null          $seller_id
 * @property string|null          $notes
 * @property int                  $daily_limit
 * @property string|null          $employer_status status used for employer-created to-dos before being confirmed by a manager
 * @property bool                 $project_title_description
 * @property string[]|null        $keywords
 * @property bool                 $private
 * @property Carbon               $created_at
 * @property Carbon               $updated_at
 * @property Carbon|null          $deleted_at
 * @property Collection|Project[] $projects
 * @property Collection|Project[] $projectsWithTrashed
 * @property Collection|Payment[] $payments
 * @property User                 $user
 */
class Todo extends Model{
  use SoftDeletes;

  const EMPLOYER_STATUSES=[
    self::STATUS_EMPLOYER_UNPAID,
    self::STATUS_EMPLOYER_UNCONFIRMED
  ];
  const STATUS_ACCEPTED='accepted';
  const STATUS_COMPLETED='completed';
  const STATUS_DELETED='deleted';
  const STATUS_EMPLOYER_UNCONFIRMED='unconfirmed';
  const STATUS_EMPLOYER_UNPAID='unpaid';
  const STATUS_UNASSIGNED='unassigned';

  public static function statusNames():array{
    return [
      self::STATUS_UNASSIGNED=>_ix('Unassigned','To-Do'),
      self::STATUS_ACCEPTED=>_ix('Accepted','To-Do'),
      self::STATUS_COMPLETED=>_ix('Completed','To-Do'),
      self::STATUS_DELETED=>_ix('Deleted','To-Do'),
      self::STATUS_EMPLOYER_UNPAID=>_ix('Unpaid','To-Do'),
      self::STATUS_EMPLOYER_UNCONFIRMED=>_ix('Unconfirmed','To-Do')
    ];
  }

  public function projects():HasMany{
    return $this->hasMany(Project::class);
  }

  public function projectsWithTrashed():HasMany{
    return $this->hasMany(Project::class)
      ->withTrashed();
  }

  public function payments():HasMany{
    return $this->hasMany(Payment::class);
  }

  public function user():BelongsTo{
    return $this->belongsTo(User::class)
      ->withTrashed();
  }

  public function status():string{
    /**
     * @var TodoManager $todoManager
     */
    $todoManager=app(TodoManager::class);

    return $todoManager->todoStatus($this);
  }

  public function statusName():string{
    return static::statusNames()[$this->status()];
  }

  protected $dates=['deleted_at'];

  protected $casts=[
    'product_price'=>'float',
    'keywords'=>'json'
  ];

  protected $visible=[
    'code',
    'fba',
    'product_url',
    'product_asin',
    'marketplace',
    'product_price',
    'product_title',
    'product_description',
    'seller_name',
    'storefront_url',
    'storefront_name',
    'seller_id',
    'notes',
    'daily_limit',
    'project_title_description',
    'keywords'
  ];
}
