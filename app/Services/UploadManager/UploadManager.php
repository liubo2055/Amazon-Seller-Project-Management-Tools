<?php

namespace Hui\Xproject\Services\UploadManager;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadManager{
  const DIRECTORY='uploads';

  public function saveUpload(UploadedFile $file,string $code):void{
    $fp=fopen($file->getRealPath(),'r');
    if(!$fp)
      throw new UploadManagerException(sprintf('Cannot open %s',$file->getRealPath()));

    // Passing $file directly would create a directory and place the file inside, which is not what we want to achieve
    Storage::put($this->uploadPath($code),$fp);

    fclose($fp);
  }

  public function saveMeta(UploadedFile $file,string $code):void{
    Storage::put($this->metaPath($code),serialize($this->fileMeta($file)));
  }

  public function loadUpload(string $code):string{
    $path=$this->uploadPath($code);
    if(!Storage::exists($path))
      throw new UploadManagerException(sprintf('Upload file %s not found',$path));

    return Storage::get($path);
  }

  public function loadMeta(string $code):FileMeta{
    $path=$this->metaPath($code);
    if(!Storage::exists($path))
      throw new UploadManagerException(sprintf('Meta file %s not found',$path));

    return unserialize(Storage::get($path));
  }

  public function deleteUploadAndMeta(string $code):void{
    Storage::delete([
      $this->metaPath($code),
      $this->uploadPath($code)
    ]);
  }

  public function code():string{
    return sprintf('%u_%s',
      time(),
      str_random(10));
  }

  private function uploadPath(string $code):string{
    return $this->path($code,'upload');
  }

  private function metaPath(string $code):string{
    return $this->path($code,'meta');
  }

  private function path(string $code,string $extension):string{
    return sprintf('%s/%s.%s',
      static::DIRECTORY,
      $code,
      $extension);
  }

  private function fileMeta(UploadedFile $file):FileMeta{
    $meta=new FileMeta;
    $meta->time=time();
    $meta->size=$file->getSize();
    $meta->name=$file->getClientOriginalName();
    $meta->mime=$file->getMimeType();

    return $meta;
  }
}
