<?php

namespace Hui\Xproject\Services\UserImporter;

use Hui\Xproject\Entities\User;

class ImportedUser{
  /**
   * @var int
   */
  public $row;

  /**
   * @var bool if false, either the user already exists or the row contains an error (if so, $error is not null)
   */
  public $imported;

  /**
   * @var User|null
   */
  public $user;

  /**
   * @var string|null
   */
  public $error;
}
