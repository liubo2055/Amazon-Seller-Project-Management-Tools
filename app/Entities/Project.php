<?php

namespace Hui\Xproject\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int           $id
 * @property string             $todo_id
 * @property string|null        $user_id
 * @property int                $number
 * @property string|null        $project_title
 * @property string|null        $product_title
 * @property string|null        $project_description
 * @property string|null        $store_description
 * @property string|null        $product_description
 * @property string|null        $notes
 * @property float              $project_price
 * @property string             $status
 * @property string|null        $completion_id
 * @property string|null        $completion_url
 * @property string|null        $original_todo_code
 * @property string|null        $original_product_description
 * @property string|null        $original_notes
 * @property float              $original_project_price
 * @property Carbon|null        $accepted_at
 * @property Carbon|null        $completion_id_submitted_at
 * @property Carbon|null        $completed_at
 * @property Carbon             $created_at
 * @property Carbon             $updated_at
 * @property Carbon|null        $deleted_at
 * @property Todo               $todo
 * @property User|null          $user
 * @property Collection|Image[] $images
 * @property Collection|Media[] $medias
 */
class Project extends Model{
  use SoftDeletes;

  const STATUSES=[
    self::STATUS_UNASSIGNED,
    self::STATUS_ACCEPTED,
    self::STATUS_WAITING_DESCRIPTION,
    self::STATUS_WAITING_URL,
    self::STATUS_WAITING_CONFIRMATION,
    self::STATUS_COMPLETED,
    self::STATUS_FAILED
  ];
  const STATUS_ACCEPTED='accepted';
  const STATUS_COMPLETED='completed';
  const STATUS_DELETED='deleted';
  const STATUS_FAILED='failed';
  const STATUS_UNASSIGNED='unassigned';
  const STATUS_WAITING_CONFIRMATION='waitingConfirmation';
  const STATUS_WAITING_DESCRIPTION='waitingDescription';
  const STATUS_WAITING_URL='waitingUrl';

  public static function statusNames():array{
    return [
      self::STATUS_UNASSIGNED=>_ix('Unassigned','Project'),
      self::STATUS_ACCEPTED=>_ix('Accepted','Project'),
      self::STATUS_WAITING_DESCRIPTION=>_ix('Waiting for description','Project'),
      self::STATUS_WAITING_URL=>_ix('Waiting for URL','Project'),
      self::STATUS_WAITING_CONFIRMATION=>_ix('Waiting for confirmation','Project'),
      self::STATUS_COMPLETED=>_ix('Completed','Project'),
      self::STATUS_FAILED=>_ix('Failed','Project'),
      self::STATUS_DELETED=>_ix('Deleted','Project')
    ];
  }

  /**
   * @param string $status
   * @return Collection|static[]
   */
  public static function findByStatusAndLock(string $status):Collection{
    return static
      ::where(compact('status'))
      ->lockForUpdate()
      ->get();
  }

  public static function statusesWithDeleted():array{
    return array_merge(static::STATUSES,[static::STATUS_DELETED]);
  }

  public function todo():BelongsTo{
    return $this->belongsTo(Todo::class);
  }

  public function user():BelongsTo{
    return $this->belongsTo(User::class);
  }

  public function images():HasMany{
    return $this->hasMany(Image::class);
  }

  public function medias():HasMany{
    return $this->hasMany(Media::class);
  }

  public function numberAndCode():string{
    return sprintf('%s-#%u',
      $this->original_todo_code,
      $this->number);
  }

  public function statusName():string{
    return static::statusNames()[$this->statusWithDeleted()];
  }

  public function statusWithDeleted():string{
    if(!$this->trashed())
      return $this->status;
    else
      return static::STATUS_DELETED;
  }

  protected $dates=[
    'accepted_at',
    'completion_id_submitted_at',
    'completed_at',
    'deleted_at'
  ];

  protected $casts=[
    'project_price'=>'float',
    'original_project_price'=>'float'
  ];
}
