<?php

namespace surangapg\Haunt\Manifest\Item;

/**

 */
class ManifestItem implements ManifestItemInterface  {

  /**
   * The uri to visit.
   *
   * @var string
   *   The uri.
   */
  protected $uri;

  /**
   * The sizes to show the screen for.
   *
   * @var array
   *   Metadata about the sizes.
   */
  protected $sizeVariations;

  /**
   * The name of the visitor visiting the site.
   *
   * @var array
   *   Metadata about the visitors.
   */
  protected $visitorVariations;

  /**
   * @inheritdoc}
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * @inheritdoc}
   */
  public function getSizeVariations() {
    return $this->sizeVariations;
  }

  /**
   * @inheritdoc}
   */
  public function getVisitorVariations() {
    return $this->visitorVariations;
  }

}