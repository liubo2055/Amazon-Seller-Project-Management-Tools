<?php

namespace Hui\Xproject\Console\Commands;

use Illuminate\Console\Command;
use Xinax\LaravelGettext\Config\ConfigManager;
use Xinax\LaravelGettext\LaravelGettext;

class ExtractVueTexts extends Command{
  public function handle(LaravelGettext $laravelGettext):void{
    $configManager=ConfigManager::create();
    $configuration=$configManager->get();

    if(empty($configuration->getSupportedLocales())){
      $this->error('Supported locales not set');

      return;
    }

    foreach($configuration->getSupportedLocales() as $locale)
      $this->extractForLocale($locale,$laravelGettext);
  }

  private function extractForLocale(string $locale,LaravelGettext $laravelGettext):void{
    $this->info(sprintf('Extracting texts for locale %s...',$locale));

    $laravelGettext->setLocale($locale);

    $texts=[];
    $this->scan(resource_path('assets/vue'),$texts);

    $path=public_path(sprintf('js/i18n/%s.js',$locale));
    $this->writeFile($path,$texts);
  }

  private function scan(string $path,array &$texts):void{
    foreach(glob(sprintf('%s/*',$path)) as $item)
      if(is_dir($item))
        $this->scan($item,$texts);
      else if(ends_with($item,'.vue')||ends_with($item,'.js'))
        $this->searchTexts($item,$texts);
  }

  private function searchTexts(string $path,array &$texts):void{
    $contents=@file_get_contents($path);
    if($contents===false){
      $this->error(sprintf('Could not open %s',$path));

      return;
    }

    $this->info(sprintf('Parsing %s...',$path),'v');

    // This would not correctly parse strings like "abc \',", but we don't expect any such texts in the application
    preg_match_all('|_ix\(\'(.+?)\',\'(.+?)\'\)|',$contents,$matches,PREG_SET_ORDER);

    foreach($matches as $match){
      [
        ,
        $message,
        $context
      ]=$match;

      $message=str_replace('\\\'','\'',$message);
      $context=str_replace('\\\'','\'',$context);

      /**
       * @see gettext.js
       */
      $key=sprintf('%s|%s',$context,$message);
      $texts[$key]=_ix($message,$context);
    }
  }

  private function writeFile(string $path,array $texts):void{
    $contents='window.messages={';

    $separator='';
    foreach($texts as $text=>$translation){
      $text=str_replace('\'','\\\'',$text);
      $translation=str_replace('\'','\\\'',$translation);

      $contents.=sprintf('%s\'%s\':\'%s\'',$separator,$text,$translation);

      if(!$separator)
        $separator=',';
    }

    $contents.='}';

    if(@file_put_contents($path,$contents))
      $this->info(sprintf('%s saved with %u texts',$path,count($texts)));
    else
      $this->error(sprintf('Could not save into %s',$path));
  }

  protected $signature='xp:extract-vue-texts';

  protected $description='Extracts gettext messages from Vue.js files and generates language JavaScript files';
}
