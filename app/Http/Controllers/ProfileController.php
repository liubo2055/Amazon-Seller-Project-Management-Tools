<?php

namespace Hui\Xproject\Http\Controllers;

use Hui\Xproject\Http\Controllers\Traits\CloudUploadTrait;
use Hui\Xproject\Services\MarketplaceInformation\MarketplaceInformation;
use Hui\Xproject\Services\UploadManager\UploadManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller{
  use CloudUploadTrait;

  public function __construct(UploadManager $uploadManager){
    $this->uploadManager=$uploadManager;
  }

  public function index():View{
    return view('profile');
  }

  public function save(Request $request,MarketplaceInformation $marketplaceInformation):JsonResponse{
    $data=$request->all();

    // As required by the "confirmed" rule
    if(!empty($data['passwordConfirmation']))
      $data['password_confirmation']=$data['passwordConfirmation'];

    $user=user();

    $rules=[
      'name'=>'required|max:255',
      'email'=>sprintf('required|email|max:255|unique:users,email,%u',$user->id),
      'password'=>'nullable|min:6|max:16|confirmed',
      'phone'=>'required|max:255',
      'timezone'=>sprintf('required|in:%s',implode(',',TIMEZONES)),
      'locale'=>sprintf('required|in:%s',implode(',',LOCALES)),
      'qq'=>'required|max:255',
      'wechatId'=>'nullable|max:255',
      'alipayId'=>'nullable|max:255',
      'companyName'=>'nullable|max:255',
      'companyUrl'=>'nullable|url|max:255'
    ];
    if(user()->isEmployer())
      $rules+=[
        'storefronts.*.url'=>'required|url|max:255',
        'storefronts.*.names.*'=>'required|max:255'
      ];
    validator($data,$rules)->validate();

    $user->name=$data['name'];
    $user->email=$data['email'];
    if(!empty($data['password']))
      $user->password=bcrypt($data['password']);
    $user->phone=$data['phone'];
    $user->timezone=$data['timezone'];
    $user->locale=$data['locale'];
    $user->qq=$data['qq'];
    $user->wechat_id=$data['wechatId'];
    $user->alipay_id=$data['alipayId'];
    $user->company_name=$data['companyName'];
    $user->company_url=$data['companyUrl'];

    if(user()->isEmployer()){
      $errors=[];

      foreach($data['storefronts'] as $index=>$storefront){
        [
          $sellerId,
          $marketplace
        ]=$marketplaceInformation->guessMerchantAndMarketplace($storefront['url']);

        if(!$sellerId||!$marketplace)
          $errors[sprintf('storefronts.%u.url',$index)]=[_ix('This is not a valid storefront URL.','Profile')];
      }

      if($errors)
        return response()->json(compact('errors'),400);

      $user->storefronts=$data['storefronts'];
    }

    $uploadCodes=[];
    if(!empty($data['fileCodes'])&&array_key_exists('image',$data['fileCodes'])){
      $code=$data['fileCodes']['image'];
      if($code!==null){
        [
          $path,
          $url
        ]=$this->uploadFile($code,user(),true);
        $uploadCodes[]=$code;
      }
      // Deleted upload
      else{
        $path=null;
        $url=null;
      }

      $user->image_path=$path;
      $user->image_url=$url;
    }

    $user->save();

    $this->deleteTempUploads($uploadCodes);

    return response()->json([
      'imageUrl'=>$user->image_url
    ]);
  }
}
