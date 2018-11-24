<?php

/**
 * @file Generator to for all the comparison png's and diff percentages.
 */
namespace surangapg\Haunt\Generator;

use surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface;
use surangapg\Haunt\Manifest\ManifestInterface;
use surangapg\Haunt\Output\Structure\OutputStructureInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class ComparisonGenerator {

  /**
   * All the items from the manifest.
   *
   * @var \surangapg\Haunt\Manifest\ManifestInterface
   *   The manifest to get the files for.
   */
  protected $manifest;

  /**
   * The set of files for the reference.
   *
   * @var \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   The reference item set with the screenshots.
   */
  protected $reference;

  /**
   * The set of files for the comparison.
   *
   * @var \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   The new data set for the comparison.
   */
  protected $new;

  /**
   * The output handler.
   *
   * @var \surangapg\Haunt\Generator\BufferedOutput|\Symfony\Component\Console\Output\OutputInterface
   *   Output handler for all the items.
   */
  protected $output;

  /**
   * The root output directory.
   *
   * @var string
   *   The directory to output the comparison files to.
   */
  protected $outputDir;

  /**
   * File system helper.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   *   File system helper.
   */
  protected $fs;

  /**
   * The array for the results of the full comparison.
   *
   * @var array
   *   The result array.
   */
  protected $results = [];

  /**
   * ComparisonGenerator constructor.
   *
   * @param \surangapg\Haunt\Manifest\ManifestInterface $manifest
   *   The manifest to check the comparisons for.
   * @param \surangapg\Haunt\Output\Structure\OutputStructureInterface $reference
   *   Reference data output.
   * @param \surangapg\Haunt\Output\Structure\OutputStructureInterface $new
   *   The new screenshot data.
   * @param string $outputDir
   *   The directory to output the data to.
   * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
   *   The output interface.
   */
  public function __construct(ManifestInterface $manifest, OutputStructureInterface $reference, OutputStructureInterface $new, string $outputDir = '', OutputInterface $output = NULL) {
    if (!isset($output)) {
      $output = new BufferedOutput();
    }

    $this->manifest = $manifest;
    $this->output = $output;
    $this->reference = $reference;
    $this->new = $new;

    if (empty($outputDir)) {
      $outputDir = getcwd();
    }

    $this->outputDir = rtrim($outputDir, '/') . '/';
    $this->outputDir .= '/' . basename($this->reference->getFolderRoot()) . '--' . basename($this->new->getFolderRoot());

  }

  /**
   * Generate all the comparison files.
   */
  public function generate() {
    $this->fs = new Filesystem();

    $this->getOutput()->writeln('<fg=yellow>Comparing snapshots</>');
    foreach ($this->manifest->listManifestItems() as $item) {
      $this->getOutput()->writeln(sprintf('   Comparing <fg=white>%s</> - %s variations', $item->getUri(), count($item->listVariations())));
      foreach($item->listVariations() as $variation) {
        $this->handleCompare($variation);
      }
    }

    // Write out the report to a yaml file.
    $this->fs->dumpFile($this->outputDir . '/results.yml', Yaml::dump($this->results, 4, 2));
  }

  /**
   * Handle a single comparison.
   *
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface $variation
   *   Variation to compare the data for.
   */
  protected function handleCompare(ManifestItemVariationLineInterface $variation) {

    $resolution = $variation->getSizeInfo();
    $this->getOutput()->writeln(sprintf('    - Checking: %s at %sx%s', $variation->getVisitor(), $resolution['width'], $resolution['height']));

    if ($this->reference->hasOutput($variation) && $this->new->hasOutput($variation)) {

      // Validate that the baseline and the current file are equal in size.
      $referenceResolution = getimagesize($this->reference->generateOutputName($variation));
      $newResolution = getimagesize($this->new->generateOutputName($variation));

      if ($referenceResolution != $newResolution) {
        $this->getOutput()->writeln('      <fg=red>Image dimensions do not match.</>');
        $this->results['comparisons'][$variation->uniqueId()] = [
          'comparison' => TRUE,
          'error' => 'Dimensions do not match.',
        ];
        return;
      }

      $diff =  $this->outputDir . '/' . $variation->uniqueId() . '.png';
      $reference = $this->reference->generateOutputName($variation);
      $new = $this->new->generateOutputName($variation);

      if (!$this->fs->exists(dirname($diff))) {
        $this->fs->mkdir(dirname($diff));
      }

      exec("compare -dissimilarity-threshold 1 -fuzz 0% -metric AE -highlight-color red $reference $new $diff 2>&1 ",$output);

      $diffPercentage = $this->calcDiffPercentage($output[0], $resolution);

      $this->output->writeln(sprintf('      Difference: %s', $this->formatPercentage($diffPercentage)));

      $this->results['comparisons'][$variation->uniqueId()] = [
        'comparison' => TRUE,
        'diff' => $diffPercentage,
        'uri' => $variation->getUri(),
        'id' => $variation->uniqueId(),
      ];
    }
    else {
      $this->results['comparisons'][$variation->uniqueId()] = [
        'comparison' => TRUE,
        'error' => 'Snapshot is not available for both sets.',
      ];
      $this->getOutput()->writeln('      <fg=red>Snapshot is not available for both sets.</>');
    }
  }

  /**
   * Calculate percentage.
   *
   * @param $diffPixels
   *   The number of pixels that are different.
   * @param array $resolution
   *   Array with the height/width key for the image.
   *
   * @return float
   *   Difference in percent.
   */
  protected function calcDiffPercentage($diffPixels, $resolution) {
    return round($diffPixels / ($resolution["width"] * $resolution["height"]) * 100, 2);
  }

  /**
   * Add a color to an output percentage.
   *
   * @param float $diffPercentage
   *   The difference in percent.
   *
   * @return string
   *   String wrapped in a pretty color.
   */
  protected function formatPercentage(float $diffPercentage) {

    if($diffPercentage < 2) {
      return '<fg=green>' . round($diffPercentage, 2) . '%</>';
    }
    elseif($diffPercentage < 10) {
      return '<fg=yellow>' . round($diffPercentage, 2) . '%</>';
    }
    else {
      return '<fg=red>' . round($diffPercentage, 2) . '%</>';
    }
  }

  /**
   * Get the output interface.
   *
   * @return \Symfony\Component\Console\Output\BufferedOutput|\Symfony\Component\Console\Output\OutputInterface
   *   Output interface being used.
   */
  public function getOutput() {
    return $this->output;
  }

}