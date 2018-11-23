<?php

namespace surangapg\Haunt\Output\Structure;

use surangapg\Haunt\Manifest\Item\ManifestItemInterface;
use surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface;

/**
 * Interface OutputStructureInterface
 *
 * Interface to structure detect the output from manifest items on a file
 * system.
 *
 * @package surangapg\Haunt\Output\Structure
 */
interface OutputStructureInterface {

  /**
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface $manifestItemVariation
   *   The manifest item to write out the data for.
   * @param $fileData
   */
  public function writeOutputFile(ManifestItemVariationLineInterface $manifestItemVariation, $fileData);

  public function findOutputFile(ManifestItemVariationLineInterface $manifestItemVariation);

  public function hasOutputFile(ManifestItemVariationLineInterface $manifestItemVariation);

  public function generateOutputFileLocation(ManifestItemVariationLineInterface $manifestItemVariation);

}
