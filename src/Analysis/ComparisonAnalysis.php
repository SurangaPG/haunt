<?php

namespace surangapg\Haunt\Analysis;

use surangapg\Haunt\Analysis\Data\ComparisonAnalysisItem;
use surangapg\Haunt\Manifest\ManifestInterface;
use surangapg\Haunt\Output\Structure\OutputStructureInterface;

class ComparisonAnalysis implements ComparisonAnalysisInterface {

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
   * The comparison output directory.
   *
   * @var \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   The directory to output the comparison files to.
   */
  protected $comparison;

  /**
   * ComparisonAnalysis constructor.
   *
   * @param \surangapg\Haunt\Manifest\ManifestInterface $manifest
   *   The manifest file to use as base
   * @param \surangapg\Haunt\Output\Structure\OutputStructureInterface $reference
   *   The location all the reference files live.
   * @param \surangapg\Haunt\Output\Structure\OutputStructureInterface $new
   *   The location all the new items live.
   * @param \surangapg\Haunt\Output\Structure\OutputStructureInterface $comparison
   *   The location all the comparison items live.
   */
  public function __construct(ManifestInterface $manifest, OutputStructureInterface $reference, OutputStructureInterface $new, OutputStructureInterface $comparison) {
    $this->manifest = $manifest;
    $this->comparison = $comparison;
    $this->reference = $reference;
    $this->new = $new;
  }

  /**
   * Generate analysis items for all the items in the manifest.
   */
  public function listItems() {

    $items = [];

    foreach ($this->manifest->listManifestItems() as $manifestItem) {
      $dataItem = new ComparisonAnalysisItem($manifestItem);
      $dataItem->extractComparisonInformation($this->comparison);

      $items[] = $dataItem;
    }

    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function getReference() {
    return $this->reference;
  }

  /**
   * {@inheritdoc}
   */
  public function getComparison() {
    return $this->comparison;
  }

  /**
   * {@inheritdoc}
   */
  public function getNew() {
    return $this->new;
  }
}
