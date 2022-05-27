<?php

namespace Hui\Xproject\Services\MenuManager;

class MenuOption{
  public function __construct(string $title,string $url,?string $icon=null,array $options=[],?int $badge=null,?bool $newTab=false){
    $this->title=$title;
    $this->url=$url;
    $this->icon=$icon;
    $this->options=$options;
    $this->badge=$badge;
    $this->newTab=$newTab;
  }

  public function isActive():bool{
    if($this->options)
      return (bool)array_filter($this->options,function(MenuOption $menuOption):bool{
        return $menuOption->isActive();
      });

    if($this->url===route('dashboard'))
      return request()->fullUrl()===route('dashboard');

    static $currentUrl;
    if(!isset($currentUrl))
      // The Route class lacks the originalParameters property in this Laravel version, so we need to use this approach
      $currentUrl=request()->fullUrl();

    if($currentUrl&&starts_with($currentUrl,$this->url))
      return $currentUrl===$this->url||in_array($currentUrl[strlen($this->url)]??null,[
          '/',
          '?'
        ]);
    else
      return false;
  }

  /**
   * @var string
   */
  public $title;

  /**
   * @var string
   */
  public $url;

  /**
   * @var string|null
   */
  public $icon;

  /**
   * @var MenuOption[]
   */
  public $options;

  /**
   * @var int|null
   */
  public $badge;

  /**
   * @var bool|null
   */
  public $newTab;
}
