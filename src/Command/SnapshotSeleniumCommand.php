<?php
/**
 * @file Contains the command that makes all the actual comparisons.
 */

namespace surangapg\Haunt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SnapshotSeleniumCommand extends Command {

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this->setName('snapshots:selenium')
      ->addOption('domain', NULL, InputOption::VALUE_REQUIRED, 'The domain to take the snapshots from.')
      ->addOption('target', NULL, InputOption::VALUE_REQUIRED, 'The type of snapshots to make (either baseline or new).', 'new')
      ->addOption('output', NULL, InputOption::VALUE_REQUIRED, 'The base location for the generated snapshots.', getcwd() . '/haunt/snapshots')
      ->setDescription('Use a selenium browser to produce a set of snapshots based on a yml config file.');
  }

  /**
   * @inheritdoc
   */
  public function initialize(InputInterface $input, OutputInterface $output) {
    parent::initialize($input, $output);

    if (!$this->isBrowserDriverActive()) {
      throw new \Exception("Selenium driver is not active. Try are you sure it was installed and is running properly? Run 'selenium-server -p 4444' if installed via brew. This is best done in a different terminal window since it's a java application that runs in the background.");
    }
  }

  /**
   * @inheritdoc
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('Work in progress');
  }

  /**
   *
   */
  protected function isBrowserDriverActive() {
    // From http://stackoverflow.com/questions/3657803/how-to-check-whether-selenium-server-is-running
    // we might want to change this to a cleaner application via curl request to http://localhost:4444/wd/hub/status
    $fp = @fsockopen('localhost', 4444);
    $active = ($fp !== false);

    if ($active) {
      fclose($fp);
    }

    return $active;
  }

}