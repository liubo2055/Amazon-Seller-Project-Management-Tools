<?php

namespace Hui\Xproject\Services\UserImporter;

class ImportResult{
  /**
   * @var int
   */
  public $imported;

  /**
   * @var int
   */
  public $errors;

  /**
   * @var int
   */
  public $existing;
}
