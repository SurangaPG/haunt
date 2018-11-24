<?php

namespace surangapg\Haunt\Output\Structure;

use surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface;
use Symfony\Component\Yaml\Yaml;

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
  public function getMetaData() {
    if (!file_exists($this->getFolderRoot() . '/meta.yml')) {
      return [];
    }

    return Yaml::parse(file_get_contents($this->getFolderRoot() . '/meta.yml'));
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

  /**
   * Get the folder root.
   *
   * @param string $root
   *   Sets the folder root.
   */
  public function setFolderRoot(string $root) {
    $this->folderRoot = $root;
  }

}
