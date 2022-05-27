<?php

namespace Hui\Xproject\Http\ViewComposers;

use Illuminate\View\View;

class ProfileComposer{
  public function compose(View $view):void{
    $saveUrl=route('profileSave');
    $uploadUrl=route('uploadPost');

    $profile=arrayCamelCase(user()->toArray());

    $timezones=array_combine(TIMEZONES,TIMEZONES);
    $timezones['Asia/Shanghai']='北京时间 Asia/Shanghai';
    $locales=[
      LOCALE_CN=>_ix('Chinese','Profile'),
      LOCALE_EN=>_ix('English','Profile')
    ];
    $showStorefronts=user()->isEmployer();

    $view->with(compact(
      'saveUrl',
      'uploadUrl',
      'profile',
      'timezones',
      'locales',
      'showStorefronts'
    ));
  }
}
