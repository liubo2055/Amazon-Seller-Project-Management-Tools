<?php

namespace Hui\Xproject\Http\Controllers;

use Hui\Xproject\Services\UploadManager\UploadManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UploadController extends Controller{
  public function __construct(UploadManager $uploadManager){
    $this->uploadManager=$uploadManager;
  }

  public function post(Request $request):JsonResponse{
    $file=$request->file('file');
    if(!$file)
      throw new HttpException(400,'File is missing');
    if(!$file->isValid())
      throw new HttpException(400,'File is not uploaded correctly');
    if($file->getSize()>30e6)
      throw new HttpException(400,'File is too big');

    $code=$this->uploadManager->code();

    $this->uploadManager->saveUpload($file,$code);
    $this->uploadManager->saveMeta($file,$code);

    $meta=$this->uploadManager->loadMeta($code);

    return response()->json([
      'code'=>$code,
      'name'=>$meta->name,
      'imageUrl'=>$this->isImage($meta->mime)?route('uploadImage',[$code]):null,
      'downloadUrl'=>route('uploadDownload',[$code]),
      'deleteUrl'=>route('uploadDelete',[$code])
    ]);
  }

  public function delete(string $code):JsonResponse{
    if((string)$code==='')
      throw new HttpException(400,'Code is missing');

    $this->uploadManager->deleteUploadAndMeta($code);

    return response()->json(compact('code'));
  }

  public function image(string $code):Response{
    if((string)$code==='')
      throw new HttpException(400,'Code is missing');

    $meta=$this->uploadManager->loadMeta($code);

    $mime=$meta->mime;
    if(!$this->isImage($mime))
      throw new HttpException(400,'The required file is not an image');

    $content=$this->uploadManager->loadUpload($code);

    return response()->make($content,200,[
      'Content-Type'=>$mime
    ]);
  }

  public function download(string $code):Response{
    if((string)$code==='')
      throw new HttpException(400,'Code is missing');

    $meta=$this->uploadManager->loadMeta($code);
    $content=$this->uploadManager->loadUpload($code);

    // response()->download() works only with local files
    return response()->make($content,200,[
      'Content-Type'=>$meta->mime,
      'Content-Disposition'=>sprintf('attachment; filename="%s"',str_replace('%','',Str::ascii($meta->name)))
    ]);
  }

  private function isImage(string $mime):bool{
    return starts_with($mime,'image/');
  }

  /**
   * @var UploadManager
   */
  private $uploadManager;
}
