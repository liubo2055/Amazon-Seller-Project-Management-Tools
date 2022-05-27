<?php

namespace Hui\Xproject\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Xinax\LaravelGettext\Config\ConfigManager;

class LocaleController extends Controller{
  public function index(Request $request):RedirectResponse{
    $configManager=ConfigManager::create();
    $configuration=$configManager->get();

    $locales=$configuration->getSupportedLocales();

    $this->validate($request,[
      'locale'=>sprintf('required|string|in:%s',implode(',',$locales)),
      'url'=>'required|string|url'
    ]);

    $locale=$request->input('locale');
    $url=$request->input('url');

    $user=user();
    if($user){
      $user->locale=$locale;
      $user->save();
    }

    changeLocale($locale);

    return redirect($url);
  }
}
