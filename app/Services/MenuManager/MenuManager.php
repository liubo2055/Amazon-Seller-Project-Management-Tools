<?php

namespace Hui\Xproject\Services\MenuManager;

use Hui\Xproject\Entities\User;

class MenuManager{
  const OPTION_DASHBOARD='dashboard';
  const OPTION_LOG_VIEWER='logViewer';
  const OPTION_PROJECTS='projects';
  const OPTION_STATISTICS='statistics';
  const OPTION_TODOS='todos';
  const OPTION_USERS='users';

  /**
   * @return MenuOption[]
   * @throws MenuManagerException
   */
  public function options():array{
    $user=user();

    $options=[];

    if($this->visible($user,static::OPTION_DASHBOARD))
      $options[]=new MenuOption(_ix('Dashboard','Menu manager'),route('dashboard'),'dashboard');
    if($this->visible($user,static::OPTION_TODOS))
      $options[]=new MenuOption(_ix('To-Dos','Menu manager'),route('todos'),'tasks');
    if($this->visible($user,static::OPTION_PROJECTS))
      $options[]=new MenuOption(_ix('Projects','Menu manager'),route('projects'),'tasks');
    if($this->visible($user,static::OPTION_STATISTICS))
      $options[]=new MenuOption(_ix('Statistics','Menu manager'),route('statistics'),'line-chart');
    if($this->visible($user,static::OPTION_USERS))
      $options[]=new MenuOption(_ix('Users','Menu manager'),route('users'),'users');
    if($this->visible($user,static::OPTION_LOG_VIEWER))
      $options[]=new MenuOption(_ix('Log Viewer','Menu manager'),route('logs'),'file-text');

    return $options;
  }

  public function visible(?User $user,string $option):bool{
    if(!$user)
      return false;

    static $config;
    if(!isset($config))
      $config=config('menu');

    if(!isset($config[$user->role]))
      throw new MenuManagerException('Wrong role');

    return in_array($option,$config[$user->role]);
  }
}
