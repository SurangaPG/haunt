<?php

/**
 * @file Component responsible for discovery of all the comparable screenshots.
 */
namespace surangapg\Haunt\Generator;

use surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface;
use surangapg\Haunt\Manifest\ManifestInterface;
use surangapg\Haunt\Output\Structure\OutputStructureInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

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

    if (empty($outputDir)) {
      $outputDir = getcwd();
    }

    $this->outputDir = rtrim($outputDir, '/') . '/';
    $this->manifest = $manifest;
    $this->output = $output;
    $this->reference = $reference;
    $this->new = $new;
  }

  /**
   * Generate all the comparison files.
   */
  public function generate() {
    $this->fs = new Filesystem();

    foreach ($this->manifest->listManifestItems() as $item) {
      foreach($item->listVariations() as $variation) {
        $this->handleCompare($variation);
      }
    }
  }

  /**
   * Handle a single comparison.
   *
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface $variation
   *   Variation to compare the data for.
   */
  protected function handleCompare(ManifestItemVariationLineInterface $variation) {
    if ($this->reference->hasOutput($variation) && $this->new->hasOutput($variation)) {
      $diff = $this->outputDir . '/'. basename($this->reference->getFolderRoot()) . '--' . basename($this->new->getFolderRoot()) . '/' . $variation->uniqueId() . '.png';
      $reference = $this->reference->generateOutputName($variation);
      $new = $this->new->generateOutputName($variation);

      if (!$this->fs->exists(dirname($diff))) {
        $this->fs->mkdir(dirname($diff));
      }

      exec("compare -dissimilarity-threshold 1 -fuzz 0% -metric AE -highlight-color red $reference $new $diff 2>&1 ",$output);
    }
  }
}