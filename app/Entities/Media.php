<?php

namespace Hui\Xproject\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string   $project_id
 * @property string   $content
 * @property Carbon   $created_at
 * @property Carbon   $updated_at
 */
class Media extends Model{
  protected $table='medias';
}
