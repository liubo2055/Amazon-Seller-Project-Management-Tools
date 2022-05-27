<?php

namespace Hui\Xproject\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class Controller extends BaseController{
  use AuthorizesRequests,
    DispatchesJobs,
    ValidatesRequests;

  public function __destruct(){
    foreach($this->tempFiles as $path)
      unlink($path);
  }

  protected function tempPath(string $base):string{
    $tempDirectory=config('hui.temp_directory');
    if(!is_dir($tempDirectory))
      if(!@mkdir($tempDirectory,0755))
        throw new HttpException(500,sprintf('Cannot create the directory "%s"',$tempDirectory));

    $path=sprintf('%s/%s-%s.tmp',
      $tempDirectory,
      $base,
      str_random());
    $this->tempFiles[]=$path;

    return $path;
  }

  private $tempFiles=[];
}
