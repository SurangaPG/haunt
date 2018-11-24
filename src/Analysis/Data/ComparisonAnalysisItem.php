<?php

namespace surangapg\Haunt\Analysis\Data;

use surangapg\Haunt\Manifest\Item\ManifestItemInterface;
use surangapg\Haunt\Output\Structure\OutputStructureInterface;

class ComparisonAnalysisItem implements ComparisonAnalysisItemInterface {

  /**
   * Number of missing comparisons for items listed in the manifest.
   *
   * @var int
   *   Number of missing comparisons.
   */
  protected $missingComparisons = 0;

  /**
   * Number of changed comparisons for items listed in the manifest.
   *
   * @var int
   *   Number of changed comparisons.
   */
  protected $changedComparisons = 0;

  /**
   * Number of unchanged comparisons for items listed in the manifest.
   *
   * @var int
   *   Number of unchanged comparisons.
   */
  protected $unchangedComparisons = 0;

  /**
   * Number of failed comparisons for items listed in the manifest.
   *
   * @var int
   *   Number of failed comparisons.
   */
  protected $failedComparisons = 0;

  /**
   * A single manifest item to check.
   *
   * @var \surangapg\Haunt\Manifest\Item\ManifestItemInterface
   *   A manifest item to check.
   */
  protected $manifestItem;

  /**
   * ComparisonAnalysisItem constructor.
   *
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemInterface $manifestItem
   *   The manifest item this analysis is for.
   */
  public function __construct(ManifestItemInterface $manifestItem) {
    $this->manifestItem = $manifestItem;
  }

  /**
   * Extract all the possible information from the comparison data.
   *
   * @param \surangapg\Haunt\Output\Structure\OutputStructureInterface $comparisonData
   *   Comparison data to extract meta information for.
   */
  public function extractComparisonInformation(OutputStructureInterface $comparisonData) {

    $comparisonArray = $comparisonData->getMetaData();
    $comparisonArray = isset($comparisonArray['comparisons']) ? $comparisonArray['comparisons'] : [];

    foreach ($this->manifestItem->listVariations() as $variation) {
      if (isset($comparisonArray[$variation->uniqueId()])) {
        $detailData = $comparisonArray[$variation->uniqueId()];

        if (!$detailData['comparison']) {
          // If for some reason the comparison could not be made.
          $this->failedComparisons++;
        }
        elseif ($detailData['diff'] == 0) {
          $this->unchangedComparisons++;
        }
        else {
          $this->changedComparisons++;
        }
      }
      else {
        // If for some reason no detail data is available in the comparison.
        $this->missingComparisons++;
      }
    }
  }

  /**
   * @return int
   */
  public function getChangedComparisons(): int {
    return $this->changedComparisons;
  }

  /**
   * @return int
   */
  public function getFailedComparisons(): int {
    return $this->failedComparisons;
  }

  /**
   * @return int
   */
  public function getMissingComparisons(): int {
    return $this->missingComparisons;
  }

  /**
   * @return int
   */
  public function getUnchangedComparisons(): int {
    return $this->unchangedComparisons;
  }
}
