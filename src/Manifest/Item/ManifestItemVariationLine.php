<?php

namespace surangapg\Haunt\Manifest\Item;

/**
 * Class ManifestItemLine
 *
 * @package surangapg\Haunt\Manifest\Item
 */
class ManifestItemVariationLine implements ManifestItemVariationLineInterface {

  /**
   * Simple machine name identifier for the size.
   *
   * @var string
   *   The size identifier.
   */
  protected $size;

  /**
   * Simple machine name identifier for the visitor.
   *
   * @var string
   *   The visitor identifier.
   */
  protected $visitor;

  /**
   * Simple keyed array for the height information.
   *
   * @TODO Make this into a class.
   *
   * @var array
   *   Array data with height/width identifiers.
   */
  protected $sizeInfo;

  /**
   * The parent item.
   *
   * @var \surangapg\Haunt\Manifest\Item\ManifestItemInterface
   *   The parent item above the variation.
   */
  protected $parent;

  /**
   * ManifestItemVariationLine constructor.
   *
   * @param string $visitor
   *   Simple machine name identifier for the visitor.
   * @param string $size
   *   Simple machine name identifier for the size.
   * @param array $sizeInfo
   *   Array data with height/width identifiers.
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemInterface $parent
   *   The parent item for this variation line.
   */
  public function __construct(string $visitor, string $size, array $sizeInfo, ManifestItemInterface $parent) {
    $this->visitor = $visitor;
    $this->size = $size;
    $this->visitor = $visitor;
    $this->sizeInfo = $sizeInfo;
    $this->parent = $parent;
  }

  /**
   * {@inheritdoc}
   */
  public function uniqueId() {
    return base64_encode($this->getUri()) . '--' . $this->getVisitor() . '--' . $this->getSize();
  }

  /**
   * {@inheritdoc}
   */
  public function getSize() {
    return $this->size;
  }

  /**
   * {@inheritdoc}
   */
  public function getVisitor() {
    return $this->visitor;
  }

  /**
   * {@inheritdoc}
   */
  public function getSizeInfo() {
    return $this->sizeInfo;
  }

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    return $this->parent;
  }

  /**
   * {@inheritdoc}
   */
  public function getUri() {
    return $this->getParent()->getUri();
  }
}
