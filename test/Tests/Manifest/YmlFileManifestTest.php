<?php

namespace surangapg\Tests\Haunt\Manifest;

use PHPUnit\Framework\TestCase;
use surangapg\Haunt\Exception\InvalidManifestConfigException;
use surangapg\Haunt\Manifest\YmlFileManifest;

/**
 * Class YmlFileManifest
 *
 * Reads in the data from a yaml file. Allowing it to be stored on the file
 * system.
 */
class YmlFileManifestTest extends TestCase {

  /**
   * @expectedException surangapg\Haunt\Exception\InvalidManifestConfigException
   */
  public function testCheckConfigMissingFileKey() {
    $config = [];

    $manifest = new YmlFileManifest($config);
    $manifest->checkConfig();
  }


  /**
   * @expectedException surangapg\Haunt\Exception\InvalidManifestConfigException
   */
  public function testCheckConfigMissingFile() {
    $config = [
      'file' => 'test/fixtures/Manifest/missing-yml-manifest.yml',
    ];

    $manifest = new YmlFileManifest($config);
    $manifest->checkConfig();
  }

  /**
   * @expectedException Symfony\Component\Yaml\Exception\ParseException
   */
  public function testCheckConfigCorruptFile() {
    $config = [
      'file' => 'test/fixtures/Manifest/corrupt-yml-manifest.yml',
    ];

    $manifest = new YmlFileManifest($config);
    $manifest->readFile();
  }

  /**
   *
   */
  public function testListManifestItems() {
    $config = [
      'file' => 'test/fixtures/Manifest/standard-yml-manifest.yml',
    ];

    $manifest = new YmlFileManifest($config);
    $items = $manifest->parseData([]);

    $this->assertCount(0, $items);
  }

}
