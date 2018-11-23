<?php

namespace surangapg\Tests\Haunt\Manifest\Output\Structure;

use PHPUnit\Framework\TestCase;
use surangapg\Haunt\Output\Structure\DefaultFolderOutputStructure;

/**
 * Class ManifestItemVariationLineTest
 *
 * Test the generating of the correct locations on the file system.
 */
class DefaultFolderOutputStructureTest extends TestCase {

  /**
   * {@inheritdoc}
   */
  public function testHasOutput() {

    $fixtureRoot = dirname(dirname(dirname(dirname(__DIR__)))) . '/fixtures/output-structure-test';

    $manifestItemVariation = $this->getMockBuilder('surangapg\Haunt\Manifest\Item\ManifestItemVariationLine')
      ->disableOriginalConstructor()
      ->setMethods(['getUri', 'getSize', 'getVisitor'])
      ->getMock();

    $manifestItemVariation->method('getUri')->willReturn('test-uri');
    $manifestItemVariation->method('getSize')->willReturn('lg');
    $manifestItemVariation->method('getVisitor')->willReturn('anonymous');

    $defaultStructure = new DefaultFolderOutputStructure($fixtureRoot);

    $this->assertEquals(TRUE, $defaultStructure->hasOutput($manifestItemVariation));

    $manifestItemVariation = $this->getMockBuilder('surangapg\Haunt\Manifest\Item\ManifestItemVariationLine')
      ->disableOriginalConstructor()
      ->setMethods(['getUri', 'getSize', 'getVisitor'])
      ->getMock();

    $manifestItemVariation->method('getUri')->willReturn('test-uri');
    $manifestItemVariation->method('getSize')->willReturn('sm');
    $manifestItemVariation->method('getVisitor')->willReturn('anonymous');

    $defaultStructure = new DefaultFolderOutputStructure($fixtureRoot);
    $this->assertEquals(FALSE, $defaultStructure->hasOutput($manifestItemVariation));
  }

  /**
   * @covers \surangapg\Haunt\Output\Structure\DefaultFolderOutputStructure::generateOutputName
   */
  public function testGenerateOutputName() {

    $manifestItemVariation = $this->getMockBuilder('surangapg\Haunt\Manifest\Item\ManifestItemVariationLine')
      ->disableOriginalConstructor()
      ->setMethods(['getUri', 'getSize', 'getVisitor'])
      ->getMock();

    $manifestItemVariation->method('getUri')->willReturn('test-uri');
    $manifestItemVariation->method('getSize')->willReturn('lg');
    $manifestItemVariation->method('getVisitor')->willReturn('anonymous');

    $defaultStructure = new DefaultFolderOutputStructure('/root/to_folder');
    $this->assertEquals('/root/to_folder/dGVzdC11cmk=/anonymous/lg/screenshot.png', $defaultStructure->generateOutputName($manifestItemVariation));

    // Test with trailing slash.
    $defaultStructure = new DefaultFolderOutputStructure('/root/to_folder/');
    $this->assertEquals('/root/to_folder/dGVzdC11cmk=/anonymous/lg/screenshot.png', $defaultStructure->generateOutputName($manifestItemVariation));
  }

  /**
   * @covers \surangapg\Haunt\Output\Structure\DefaultFolderOutputStructure::getFolderRoot
   */
  public function testGetFolderRoot() {

    $defaultStructure = new DefaultFolderOutputStructure('/root/to_folder');
    $this->assertEquals('/root/to_folder', $defaultStructure->getFolderRoot());
  }

}
