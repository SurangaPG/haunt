<?php
/**
 * @file Contains the command that makes all the actual comparisons.
 */

namespace surangapg\Haunt\Command;

use surangapg\Haunt\Component\Comparison;
use surangapg\Haunt\Component\Discovery;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompareCommand extends Command {

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this->setName('compare')
      ->addOption('source', NULL, InputOption::VALUE_REQUIRED, 'The directory where the source files are located.', getcwd() . '/haunt/snapshots')
      ->setDescription('Compare all the different screenshots for the project.');
  }

  /**
   * @inheritdoc
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $sourceDir = $input->getOption('source');

    $discovery = new Discovery($sourceDir, null, null, $output);
    $folders = $discovery->discover();

    if (count($folders) == 0) {
      $output->writeln('No valid items found for comparison. Aborting.');
      $output->writeln('');
      return;
    }

    // Otherwise start comparing images.
    $comparison = new Comparison($folders, $output);
    $comparison->compare();

    // Write out the report.
    $jsonFile = fopen($sourceDir . '/report.json', "w") or die("Unable to open file!");
    $txt = json_encode($comparison->getReport()->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    fwrite($jsonFile, $txt);
    fclose($jsonFile);

    $output->writeln('');
    $output->writeln('<fg=white>Completed comparison.</>');
  }

}