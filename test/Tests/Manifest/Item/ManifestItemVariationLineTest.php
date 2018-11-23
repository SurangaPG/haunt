<?php

namespace surangapg\Tests\Haunt\Manifest\Item;

use PHPUnit\Framework\TestCase;
use surangapg\Haunt\Manifest\Item\ManifestItemVariationLine;

/**
 * Class ManifestItemTest
 *
 * Tests a single item in a manifest.
 */
class ManifestItemVariationLineTest extends TestCase {

  /**
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItemVariationLine::uniqueId
   */
  public function testUniqueId() {
    $manifestItem = $this->getMockBuilder('surangapg\Haunt\Manifest\Item\ManifestItem')
      ->setConstructorArgs(['test-uri'])
      ->setMethods(['getUri'])
      ->getMock();
    $manifestItem->method('getUri')->willReturn('test-uri');
    $manifestItemVariationLine = new ManifestItemVariationLine('anonymous', 'lg', ['width' => 300, 'height' => 200], $manifestItem);

    $this->assertEquals('dGVzdC11cmk=--anonymous--lg', $manifestItemVariationLine->uniqueId());
  }

  /**
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItemVariationLine::getSize
   */
  public function testGetSize() {
    $manifestItem = $this->getMockBuilder('surangapg\Haunt\Manifest\Item\ManifestItem')
      ->setConstructorArgs(['test-uri'])
      ->setMethods(['getUri'])
      ->getMock();
    $manifestItem->method('getUri')->willReturn('test-uri');
    $manifestItemVariationLine = new ManifestItemVariationLine('anonymous', 'lg', ['width' => 300, 'height' => 200], $manifestItem);

    $this->assertEquals('lg', $manifestItemVariationLine->getSize());
  }

  /**
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItemVariationLine::getVisitor
   */
  public function testGetVisitor() {
    $manifestItem = $this->getMockBuilder('surangapg\Haunt\Manifest\Item\ManifestItem')
      ->setConstructorArgs(['test-uri'])
      ->setMethods(['getUri'])
      ->getMock();
    $manifestItem->method('getUri')->willReturn('test-uri');
    $manifestItemVariationLine = new ManifestItemVariationLine('anonymous', 'lg', ['width' => 300, 'height' => 200], $manifestItem);

    $this->assertEquals('anonymous', $manifestItemVariationLine->getVisitor());
  }

  /**
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItemVariationLine::getParent
   */
  public function testGetParent() {
    $manifestItem = $this->getMockBuilder('surangapg\Haunt\Manifest\Item\ManifestItem')
      ->setConstructorArgs(['test-uri'])
      ->setMethods(['getUri'])
      ->getMock();
    $manifestItem->method('getUri')->willReturn('test-uri');
    $manifestItemVariationLine = new ManifestItemVariationLine('anonymous', 'lg', ['width' => 300, 'height' => 200], $manifestItem);

    $this->assertEquals($manifestItem, $manifestItemVariationLine->getParent());
  }

  /**
   * @covers \surangapg\Haunt\Manifest\Item\ManifestItemVariationLine::getUri
   */
  public function testGetUri() {
    $manifestItem = $this->getMockBuilder('surangapg\Haunt\Manifest\Item\ManifestItem')
      ->setConstructorArgs(['test-uri'])
      ->setMethods(['getUri'])
      ->getMock();
    $manifestItem->method('getUri')->willReturn('test-uri');
    $manifestItemVariationLine = new ManifestItemVariationLine('anonymous', 'lg', ['width' => 300, 'height' => 200], $manifestItem);

    $this->assertEquals('test-uri', $manifestItemVariationLine->getUri());
  }

}

