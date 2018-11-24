<?php
/**
 * @file Contains the command that makes all the actual comparisons.
 */

namespace surangapg\Haunt\Command;

use surangapg\Haunt\Analysis\ComparisonAnalysis;
use surangapg\Haunt\Generator\ArtifactGenerator;
use surangapg\Haunt\Manifest\YamlFileManifest;
use surangapg\Haunt\Output\Structure\DefaultFolderOutputStructure;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ArtifactCommand extends Command {

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this->setName('artifact')
      ->addOption('reference', NULL, InputOption::VALUE_REQUIRED, 'The directory where the source files are located.', getcwd() . '/haunt/snapshots/reference')
      ->addOption('new', NULL, InputOption::VALUE_REQUIRED, 'The directory where the source files are located.', getcwd() . '/haunt/snapshots/new')
      ->addOption('comparison', NULL, InputOption::VALUE_REQUIRED, 'The directory to output the comparison to..', getcwd() . '/haunt/results')
      ->addOption('manifest', NULL, InputOption::VALUE_REQUIRED, 'The manifest file to use.')
      ->addOption('artifact-dir', NULL, InputOption::VALUE_REQUIRED, 'The directory to put the artifact in.')
      ->setDescription('Generate static html output of a given type.');
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

    $comparison = $input->getOption('comparison');
    $comparison = rtrim($comparison, '/') . '/';
    $comparison .= '/' . basename($referenceData->getFolderRoot()) . '--' . basename($newData->getFolderRoot());
    $comparison = new DefaultFolderOutputStructure($comparison);

    $artifactDir = $input->getOption('artifact-dir');

    $analysis = new ComparisonAnalysis($manifest, $referenceData, $newData, $comparison);
    $analysis->listItems();

    // @TODO Make the type of artifact swappable.
    $artifactGenerator = new ArtifactGenerator($analysis, $artifactDir, $output);
    $artifactGenerator->generate();
  }
}
