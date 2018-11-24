<?php

namespace surangapg\Haunt\Analysis;

interface ComparisonAnalysisInterface {

  /**
   * List all the items.
   *
   * @return \surangapg\Haunt\Analysis\Data\ComparisonAnalysisItemInterface[]
   *   The analysis for all the items.
   */
  public function listItems();

  /**
   * The reference data structure.
   *
   * @return \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   Raw reference data structure.
   */
  public function getReference();

  /**
   * The raw comparison structure.
   *
   * @return \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   The raw comparison structure.
   */
  public function getComparison();

  /**
   * The raw new structure.
   *
   * @return \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   The raw new structure.
   */
  public function getNew();

}
