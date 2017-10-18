<?php
/**
 * @file Contains the command that makes all the actual comparisons.
 */

namespace surangapg\Haunt\Command;

use surangapg\Haunt\Component\Discovery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompareCommand extends Command {

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this->setName('compare')
      ->setDescription('Compare all the different screenshots for the project.');
  }

  /**
   * @inheritdoc
   */
  public function run(InputInterface $input, OutputInterface $output) {

    $discovery = new Discovery(getcwd(), null, null, $output);
    $discovery->discover();
  }

}