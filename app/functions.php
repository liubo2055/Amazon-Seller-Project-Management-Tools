<?php

use Carbon\Carbon;
use Hui\Xproject\Entities\User;
use Illuminate\Support\Facades\Route;
use Xinax\LaravelGettext\Facades\LaravelGettext;

function _ix(string $message,string $context,$args=null):string{
  $prefix=sprintf("%s\004",$context);
  $translation=_i($prefix.$message,$args);

  // If text is not translated, remove the prefix
  if(!starts_with($translation,$prefix))
    return $translation;
  else
    return $message;
}

function arrayCamelCase(array $array):array{
  foreach($array as $key=>$value){
    if(is_array($value)&&$value)
      $array[$key]=arrayCamelCase($value);

    if(strpos($key,'_')!==false){
      $array[camel_case($key)]=$value;
      unset($array[$key]);
    }
  }

  return $array;
}

function changeLocale(string $locale):void{
  LaravelGettext::setLocale($locale);
  app()->setLocale(substr($locale,0,2));
}

function dateText(Carbon $date):string{
  $now=now();

  if($date->isPast())
    if(($days=$date->diffInDays($now))<=6)
      if($days>=2)
        return sprintf(_ix('%u days ago','Date'),$days);
      else if($days===1)
        return _ix('One day ago','Date');
      else if(($hours=$date->diffInHours($now))>=2)
        return sprintf(_ix('%u hours ago','Date'),$hours);
      else if($hours===1)
        return _ix('One hour ago','Date');
      else if(($minutes=$date->diffInMinutes($now))>=2)
        return sprintf(_ix('%u minutes ago','Date'),$minutes);
      else if($minutes===1)
        return _ix('One minute ago','Date');
      else if(($seconds=$date->diffInSeconds($now))>=2)
        return sprintf(_ix('%u seconds ago','Date'),$seconds);
      else if($seconds===1)
        return _ix('One second ago','Date');
      else
        return _ix('Now','Date');

  return $date->format('Y-m-d H:i');
}

function isActiveRoute(string $route,string $output='active'):?string{
  /** @noinspection PhpUndefinedMethodInspection */
  if(Route::currentRouteName()==$route)
    return $output;
  else
    return null;
}

function jsObject(array $array,bool $object=true):string{
  if(!$array)
    return $object?'{}':'[]';

  if(!$object)
    $array=array_values($array);

  $js=json_encode($array,JSON_UNESCAPED_UNICODE|JSON_HEX_QUOT|JSON_HEX_APOS);
  $js=htmlspecialchars($js);

  return $js;
}

function multilineHtml(?string $text):?string{
  if($text!==null)
    return nl2br(strip_tags($text));
  else
    return null;
}

function userLocale():string{
  $user=user();

  if($user&&$user->locale)
    return user()->locale;
  else
    return LaravelGettext::getLocale();
}

function user():?User{
  /** @noinspection PhpIncompatibleReturnTypeInspection */
  return auth()->user();
}
