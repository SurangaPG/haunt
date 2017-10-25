<?php
/**
 * @file Contains the command that makes all the actual comparisons.
 */

namespace surangapg\Haunt\Command;

use surangapg\Haunt\Component\Html;
use surangapg\Haunt\Output\ComparisonOutput;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OutputCommand extends Command {

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this->setName('generate-html')
      ->addOption('source', NULL, InputOption::VALUE_REQUIRED, 'The report file to get the data from.', getcwd() . '/report.json')
      ->addOption('target', NULL, InputOption::VALUE_REQUIRED, 'The target directory to place the html in.', getcwd())
      ->setDescription('Generate static html output of a given type.');
  }

  /**
   * @inheritdoc
   */
  public function initialize(InputInterface $input, OutputInterface $output) {
    parent::initialize($input, $output);

    $source = $input->getOption('source');
    if (!isset($source)) {
      throw new \Exception('Source option is required.');
    }

    if (!file_exists($source)) {
      throw new \Exception(sprintf('Source file %s doesn\'t exist. You can specify one (absolute path only currently) via --source.', $source));
    }

  }

  /**
   * @inheritdoc
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('Generating static html');

    // Currently only one type of output can be generated.
    $outputType = new ComparisonOutput();

    // Get the data from the file.
    // @TODO Validate the json file here.
    $reportData = json_decode(file_get_contents($input->getOption('source')), TRUE);

    $html = new Html($reportData, $outputType, $output);
    $html->writeHtml($input->getOption('target'));
  }
}