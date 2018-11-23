<?php

namespace surangapg\Haunt\Manifest\Item;

/**
 * Interface ManifestItemVariationLineInterface
 *
 * A single line in a Manifest item for a variation. Generated by the
 * manifest file.
 *
 * @package surangapg\Haunt\Manifest\Item
 */
interface ManifestItemVariationLineInterface {

  /**
   * Get a clean identifier for this item.
   *
   * @return string
   *   Unique clean identifier string for this item.
   */
  public function uniqueId();

  /**
   * Simple identifier information for the size.
   *
   * @return string
   *   The id for the size.
   */
  public function getSize();

  /**
   * Simple identifier information for the visitor.
   *
   * @return array
   *   The id for the visitor.
   */
  public function getVisitor();

  /**
   * The manifest parent item.
   *
   * @return \surangapg\Haunt\Manifest\Item\ManifestItemInterface
   *   The parent manifest item.
   */
  public function getParent();

  /**
   * The uri for the variation line.
   *
   * @return string
   *   The uri to visit.
   */
  public function getUri();

}