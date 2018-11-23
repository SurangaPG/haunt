<?php

namespace surangapg\Haunt\Output\Structure;

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
   * Checks or output exists for this item.
   *
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface $manifestItemVariation
   *   The manifest item to write out the data for.
   */
  public function hasOutput(ManifestItemVariationLineInterface $manifestItemVariation);

  /**
   * Generates the file name for the file that has the output for this manifest file.
   *
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface $manifestItemVariation
   *   The variation to check the data for.
   *
   * @return string
   *   The location for the file.
   */
  public function generateOutputName(ManifestItemVariationLineInterface $manifestItemVariation);

  /**
   * Get the root folder.
   *
   * @return string
   *   The folder root.
   */
  public function getFolderRoot();
}
