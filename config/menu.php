<?php

use Hui\Xproject\Entities\User;
use Hui\Xproject\Services\MenuManager\MenuManager;

return [
  User::ROLE_ADMIN=>[
    MenuManager::OPTION_DASHBOARD,
    MenuManager::OPTION_TODOS,
    MenuManager::OPTION_PROJECTS,
    MenuManager::OPTION_STATISTICS,
    MenuManager::OPTION_USERS,
    MenuManager::OPTION_LOG_VIEWER
  ],
  User::ROLE_MANAGER=>[
    MenuManager::OPTION_DASHBOARD,
    MenuManager::OPTION_TODOS,
    MenuManager::OPTION_PROJECTS,
    MenuManager::OPTION_STATISTICS,
    MenuManager::OPTION_USERS
  ],
  User::ROLE_FREELANCER=>[
    MenuManager::OPTION_DASHBOARD,
    MenuManager::OPTION_TODOS,
    MenuManager::OPTION_PROJECTS,
    MenuManager::OPTION_STATISTICS
  ],
  User::ROLE_EMPLOYER=>[
    MenuManager::OPTION_DASHBOARD,
    MenuManager::OPTION_TODOS,
    MenuManager::OPTION_PROJECTS,
    MenuManager::OPTION_STATISTICS,
    MenuManager::OPTION_USERS
  ],
  User::ROLE_FREE_EMPLOYER=>[
    MenuManager::OPTION_DASHBOARD,
    MenuManager::OPTION_TODOS,
    MenuManager::OPTION_PROJECTS,
    MenuManager::OPTION_STATISTICS,
    MenuManager::OPTION_USERS
  ]
];
