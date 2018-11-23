<?php

namespace surangapg\Haunt\Manifest\Item;

/**
 * Represents a single item in a manifest.
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
   * All the variation items.
   *
   * @var \surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface[]
   *   All the generated variations.
   */
  protected $variations;

  /**
   * ManifestItem constructor.
   *
   * @param string $uri
   *   The base uri to visit.
   * @param array $data
   *   All the data for the manifest item.
   */
  public function __construct(string $uri, array $data = []) {
    $this->uri = $uri;
    $this->sizeVariations = isset($data['sizes']) ? $data['sizes'] : ['default' => ['width' => 1200, 'height' => 800]];
    $this->visitorVariations = isset($data['visitors']) ? $data['visitors'] : ['default'];
  }

  /**
   * @inheritdoc}
   */
  public function listVariations() {
    if (empty($this->variations)) {
      foreach ($this->getSizeVariations() as $sizeVariationKey => $sizeInfo) {
        foreach ($this->getVisitorVariations() as $visitorVariation) {
          $this->variations[] = new ManifestItemVariationLine($visitorVariation, $sizeVariationKey, $sizeInfo, $this);
        }
      }
    }
    return $this->variations;
  }

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