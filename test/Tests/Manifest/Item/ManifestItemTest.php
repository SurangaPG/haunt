<?php

namespace surangapg\Tests\Haunt\Manifest\Item;

use PHPUnit\Framework\TestCase;
use surangapg\Haunt\Manifest\Item\ManifestItem;

/**
 * Class ManifestItemTest
 *
 * Tests a single item in a manifest.
 */
class ManifestItemTest extends TestCase {

  /**
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItem::__construct
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItem::getSizeVariations
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItem::getVisitorVariations
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItem::getUri
   */
  public function testConstruct() {
    // Check that the correct defaults are added.
    $manifestItem = new ManifestItem('test-uri');

    $this->assertEquals('test-uri', $manifestItem->getUri());
    $this->assertArrayHasKey('default', $manifestItem->getSizeVariations());
    $this->assertEquals(['default' => ['width' => 1200, 'height' => 800]], $manifestItem->getSizeVariations());
    $this->assertEquals(['default'], $manifestItem->getVisitorVariations());

    // Check basic setting of data.
    $data = [
      'sizes' => [
        'lg' => [
          'width' => 800,
          'height' => 300,
        ],
      ],
      'visitors' => [
        'jan-met-de-pet'
      ],
    ];

    $manifestItem = new ManifestItem('test-uri', $data);
    $this->assertEquals('test-uri', $manifestItem->getUri());
    $this->assertArrayHasKey('lg', $manifestItem->getSizeVariations());
    $this->assertEquals(['lg' => ['width' => 800, 'height' => 300]], $manifestItem->getSizeVariations());
    $this->assertEquals(['jan-met-de-pet'], $manifestItem->getVisitorVariations());
  }
  
}

