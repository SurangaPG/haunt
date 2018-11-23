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

  /**
   * {@inheritdoc}
   */
  public function hasOutput(ManifestItemVariationLineInterface $manifestItemVariation) {
    return file_exists($this->generateOutputName($manifestItemVariation));
  }

  /**
   * {@inheritdoc}
   */
  public function generateOutputName(ManifestItemVariationLineInterface $manifestItemVariation) {
    return rtrim($this->folderRoot, '/') . '/' . base64_encode($manifestItemVariation->getUri()) . '/' . $manifestItemVariation->getVisitor() . '/' . $manifestItemVariation->getSize() .'/screenshot.png';
  }

  /**
   * {@inheritdoc}
   */
  public function getFolderRoot() {
    return $this->folderRoot;
  }

}
