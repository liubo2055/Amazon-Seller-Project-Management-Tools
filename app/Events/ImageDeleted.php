<?php

namespace Hui\Xproject\Events;

use Hui\Xproject\Entities\Image;
use Illuminate\Queue\SerializesModels;

class ImageDeleted{
  use SerializesModels;

  public function __construct(Image $image){
    $this->image=$image;
  }

  /**
   * @var Image
   */
  public $image;
}
