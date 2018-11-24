<?php
/**
 * @file Contains the command that makes all the actual comparisons.
 */

namespace surangapg\Haunt\Command;

use surangapg\Haunt\Component\Comparison;
use surangapg\Haunt\Component\Discovery;
use surangapg\Haunt\Generator\ComparisonGenerator;
use surangapg\Haunt\Manifest\YamlFileManifest;
use surangapg\Haunt\Output\Structure\DefaultFolderOutputStructure;
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
      ->addOption('reference', NULL, InputOption::VALUE_REQUIRED, 'The directory where the source files are located.', getcwd() . '/haunt/snapshots/reference')
      ->addOption('new', NULL, InputOption::VALUE_REQUIRED, 'The directory where the source files are located.', getcwd() . '/haunt/snapshots/new')
      ->addOption('output-dir', NULL, InputOption::VALUE_REQUIRED, 'The directory to output the comparison to..', getcwd() . '/haunt/results')
      ->addOption('manifest', NULL, InputOption::VALUE_REQUIRED, 'The manifest file to use.')
      ->setDescription('Compare all the different screenshots for the project.');
  }

  /**
   * @inheritdoc
   */
  public function execute(InputInterface $input, OutputInterface $output) {
    $currentDir = $input->getOption('reference');
    $referenceData = new DefaultFolderOutputStructure($currentDir);

    $newDir = $input->getOption('new');
    $newData = new DefaultFolderOutputStructure($newDir);

    $manifest = $input->getOption('manifest');
    $manifest = new YamlFileManifest(['file' => $manifest]);

    $outputDir = $input->getOption('output-dir');
    $outputDir = rtrim($outputDir, '/') . '/';
    $outputDir .= '/' . basename($referenceData->getFolderRoot()) . '--' . basename($newData->getFolderRoot());
    $outputDir = new DefaultFolderOutputStructure($outputDir);

    $comparisonGenerator = new ComparisonGenerator($manifest, $referenceData, $newData, $outputDir, $output);
    $comparisonGenerator->generate();

  }

}