<?php

namespace surangapg\Haunt;

use Symfony\Component\Console\Application;
use surangapg\Haunt\Command\CompareCommand;

class HauntApplication extends Application {

  /**
   * HauntApplication constructor.
   *
   * @inheritdoc
   */
  public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN') {
    parent::__construct($name, $version);
    $this->add(new CompareCommand());
  }
}