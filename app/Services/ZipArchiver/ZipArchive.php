<?php

namespace Hui\Xproject\Services\ZipArchiver;

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Support\Facades\Storage;
use ZipArchive as Zip;

class ZipArchive{
  public function addFile(string $path):void{
    $this->filePaths[]=$path;
  }

  public function addInlineFile(InlineFile $file):void{
    $this->inlineFiles[]=$file;
  }

  public function archive(string $path):void{
    if(!$this->filePaths&&!$this->inlineFiles)
      throw new ZipArchiverException('Cannot create an empty ZIP');

    $zip=new Zip;
    if(!$zip->open($path,Zip::CREATE|Zip::OVERWRITE))
      throw new ZipArchiverException('Could not create the ZIP archive');

    /**
     * @var Cloud $disk
     */
    $disk=Storage::cloud();

    foreach($this->filePaths as $filePath)
      $zip->addFromString(basename($filePath),$disk->get($filePath));
    /**
     * @var InlineFile $file
     */
    foreach($this->inlineFiles as $file)
      $zip->addFromString($file->name,$file->content);

    $zip->close();
  }

  /**
   * @var string[]
   */
  private $filePaths=[];

  /**
   * @var InlineFile
   */
  private $inlineFiles=[];
}
