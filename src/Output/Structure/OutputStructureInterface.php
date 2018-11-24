<?php

namespace surangapg\Haunt\Output\Structure;

use surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface;

/**
 * Interface OutputStructureInterface
 *
 * Interface to structure detect the output from manifest items on a file
 * system.
 *
 * @TODO Every one of these sets has a meta.yml in the root. Accessing this
 * should be done via this interface.
 *
 * @package surangapg\Haunt\Output\Structure
 */
interface OutputStructureInterface {

  /**
   * Gets all the metadata about this output.
   *
   * @TODO Untested.
   *
   * @return array
   *   Get all the metadata for this item.
   */
  public function getMetaData();

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

  /**
   * Get the folder root.
   *
   * @param string $root
   *   Sets the folder root.
   */
  public function setFolderRoot(string $root);
}
