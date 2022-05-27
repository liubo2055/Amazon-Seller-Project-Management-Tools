<?php

namespace Hui\Xproject\Listeners;

use Hui\Xproject\Events\ImageDeleted;
use Illuminate\Support\Facades\Storage;

class ImageDeletedListener{
  public function handle(ImageDeleted $imageDeleted):void{
    $image=$imageDeleted->image;

    Storage::cloud()
      ->delete($image->path);

    logger(sprintf('File %s deleted from S3 (deleted image %u)',
      $image->path,
      $image->id));
  }
}
