<?php

namespace surangapg\Haunt\Output\Structure;

use surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface;

/**
 * Class DefaultFolderOutputStructure
 *
 * Basic folder structure to write out the files.
 *
 * @package surangapg\Haunt\Output\Structure
 */
class DefaultFolderOutputStructure implements OutputStructureInterface {

  /**
   * The root folder.
   *
   * @var string
   *   The root folder to work from.
   */
  protected $folderRoot;

  /**
   * DefaultFolderOutputStructure constructor.
   *
   * @param string $folderRoot
   *   The folder root for this item.
   */
  public function __construct($folderRoot) {
    $this->folderRoot = $folderRoot;
  }

  public function writeOutputFile(ManifestItemVariationLineInterface $manifestItemVariation, $fileData) {

  }

  public function findOutputFile(ManifestItemVariationLineInterface $manifestItemVariation) {

  }

  public function hasOutputFile(ManifestItemVariationLineInterface $manifestItemVariation) {

  }

  public function generateOutputFileLocation(ManifestItemVariationLineInterface $manifestItemVariation) {

  }

  /**
   * Get the root folder.
   *
   * @return string
   *   The folder root.
   */
  public function getFolderRoot(): string {
    return $this->folderRoot;
  }

  /**
   * Set the root folder.
   *
   * @param string $folderRoot
   *   The folder root.
   */
  public function setFolderRoot(string $folderRoot) {
    $this->folderRoot = $folderRoot;
  }
}
