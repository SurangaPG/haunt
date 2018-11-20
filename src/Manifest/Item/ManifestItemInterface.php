<?php

namespace surangapg\Haunt\Manifest\Item;

/**
 * Interface ManifestItemInterface
 *
 * This is a single item in the manifest that contains some metadata about
 * the variations for a screenshot.
 *
 * @package surangapg\Haunt\Manifest
 */
interface ManifestItemInterface {

  /**
   * Basic uri where the page can be found.
   *
   * @return string
   *   The uri where the screenshot should be taken.
   */
  public function getUri();

  /**
   * A list of all the size variations that need a screenshot.
   *
   * @return array
   *   All the different variations as keyed by source.
   */
  public function getSizeVariations();

  /**
   * A list of all the visitor variations that need a screenshot.
   *
   * @return array
   *   All the different variations as keyed by source.
   */
  public function getVisitorVariations();

}
