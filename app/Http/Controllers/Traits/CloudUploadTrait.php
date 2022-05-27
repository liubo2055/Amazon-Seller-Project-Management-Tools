<?php

namespace Hui\Xproject\Http\Controllers\Traits;

use Hui\Xproject\Entities\User;
use Hui\Xproject\Services\UploadManager\FileMeta;
use Hui\Xproject\Services\UploadManager\UploadManager;
use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait CloudUploadTrait{
  protected function uploadFile(string $code,User $user,bool $imageRequired):array{
    $meta=$this->uploadManager->loadMeta($code);
    if($imageRequired&&!$this->isImage($meta->mime))
      throw new HttpException(400,'The uploaded file is not an image');

    $content=$this->uploadManager->loadUpload($code);
    $path=$this->path($meta,$user);

    /**
     * @var Cloud $disk
     */
    $disk=Storage::cloud();

    $disk->put($path,$content,'public');
    $url=$disk->url($path);

    logger(sprintf('File %s (code %s) uploaded to cloud (%s)',
      $meta->name,
      $code,
      $url));

    return [
      $path,
      $url
    ];
  }

  protected function isImage(string $mime):bool{
    return starts_with($mime,'image/');
  }

  protected function deleteTempUploads(array $uploadCodes):void{
    foreach($uploadCodes as $code)
      $this->uploadManager->deleteUploadAndMeta($code);
  }

  private function path(FileMeta $meta,User $user):string{
    return sprintf('%u/user-%u/%s-%u-%s.%s',
      now()->year,
      $user->id,
      pathinfo($meta->name,PATHINFO_FILENAME),
      time(),
      str_random(),
      pathinfo($meta->name,PATHINFO_EXTENSION));
  }

  /**
   * @var UploadManager
   */
  protected $uploadManager;
}
