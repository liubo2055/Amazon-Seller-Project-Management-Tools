<?php

namespace Hui\Xproject\Entities;

use Carbon\Carbon;
use Hui\Xproject\Events\ImageDeleted;
use Hui\Xproject\Events\ImageUpdated;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property string   $project_id
 * @property string   $path
 * @property string   $url
 * @property Carbon   $created_at
 * @property Carbon   $updated_at
 */
class Image extends Model{
  protected $dispatchesEvents=[
    'updated'=>ImageUpdated::class,
    'deleted'=>ImageDeleted::class
  ];
}
