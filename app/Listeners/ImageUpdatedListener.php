<?php

namespace Hui\Xproject\Listeners;

use Hui\Xproject\Events\ImageUpdated;
use Illuminate\Support\Facades\Storage;

class ImageUpdatedListener{
  public function handle(ImageUpdated $imageUpdated):void{
    $image=$imageUpdated->image;

    $previousPath=$image->getOriginal('path');
    if($previousPath&&$previousPath!==$image->path){
      Storage::cloud()
        ->delete($previousPath);

      logger(sprintf('File %s deleted from S3 (updated image %u)',
        $previousPath,
        $image->id));
    }
  }
}
