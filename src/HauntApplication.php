<?php

namespace surangapg\Haunt;

use surangapg\Haunt\Command\OutputCommand;
use surangapg\Haunt\Command\SnapshotSeleniumCommand;
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
    $this->add(new SnapshotSeleniumCommand());
    $this->add(new OutputCommand());
  }
}