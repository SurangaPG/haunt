<?php

namespace surangapg\Tests\Haunt\Manifest;

use PHPUnit\Framework\TestCase;
use surangapg\Haunt\Manifest\YamlFileManifest;

/**
 * Class YmlFileManifest
 *
 * Reads in the data from a yaml file. Allowing it to be stored on the file
 * system.
 */
class YamlFileManifestTest extends TestCase {

  /**
   * @covers \surangapg\Haunt\Manifest\YamlFileManifest::__construct
   *
   * @expectedException surangapg\Haunt\Exception\InvalidManifestConfigException
   */
  public function testCheckConfigMissingFileKey() {
    $config = [];

    $manifest = new YamlFileManifest($config);
    $manifest->checkConfig();
  }

  /**
   * @covers \surangapg\Haunt\Manifest\YamlFileManifest::listManifestItems
   */
  public function testListManifestItems() {
    $config = [
      'file' => 'test/fixtures/Manifest/standard-yml-manifest.yml',
    ];

    $manifest = new YamlFileManifest($config);
    $items = $manifest->listManifestItems();

    $this->assertCount(6, $items);
  }

  /**
   * @covers \surangapg\Haunt\Manifest\YamlFileManifest::checkConfig
   *
   * @expectedException surangapg\Haunt\Exception\InvalidManifestConfigException
   */
  public function testCheckConfigMissingFile() {
    $config = [
      'file' => 'test/fixtures/Manifest/missing-yml-manifest.yml',
    ];

    $manifest = new YamlFileManifest($config);
    $manifest->checkConfig();
  }

  /**
   * @covers \surangapg\Haunt\Manifest\YamlFileManifest::checkConfig
   */
  public function testParseDataSimple() {
    $config = [
      'file' => 'test/fixtures/Manifest/standard-yml-manifest.yml',
    ];

    $rawData = [
      'default_variations' => [
        'sizes' => [
          'xs' => [
            'height' => 10,
            'width' => 10,
          ],
          'lg' => [
            'height' => 10,
            'width' => 10,
          ],
        ],
        'users' => [
          'anonymous',
        ],
      ],
      'paths' => [
        '/',
        '/contact',
      ],
    ];

    $manifest = new YamlFileManifest($config);
    $parsedData = $manifest->parseData($rawData);

    $this->assertCount(2, $parsedData, "2 items should have been detected in the raw data.");

    $this->assertEquals('/', $parsedData[0]['path'], "The path key should be correct.");
    $this->assertCount(2, $parsedData[0]['sizes'], "The 'sizes' key should have matched the default variations.");
    $this->assertCount(1, $parsedData[0]['users'], "The 'users' key should have matched the default variations.");

    $this->assertEquals('/contact', $parsedData[1]['path'], "The path key should be correct.");
    $this->assertCount(2, $parsedData[1]['sizes'], "The 'sizes' key should have matched the default variations.");
    $this->assertCount(1, $parsedData[1]['users'], "The 'users' key should have matched the default variations.");

  }

  /**
   * @covers \surangapg\Haunt\Manifest\YamlFileManifest::checkConfig
   */
  public function testParseDataOverwrite() {
    $config = [
      'file' => 'test/fixtures/Manifest/corrupt-yml-manifest.yml',
    ];

    $rawData = [
      'default_variations' => [
        'sizes' => [
          'xs' => [
            'height' => 10,
            'width' => 10,
          ],
          'lg' => [
            'height' => 10,
            'width' => 10,
          ],
        ],
        'users' => [
          'anonymous',
        ],
      ],
      'paths' => [
        [
          'path' => '/',
          'sizes' => [
            'sm' => [
              'height' => 10,
              'width' => 10,
            ],
          ],
          'users' => [
            'anonymous',
            'jan',
            'karel',
          ],
        ],
        '/contact',
      ],
    ];

    $manifest = new YamlFileManifest($config);
    $parsedData = $manifest->parseData($rawData);

    $this->assertCount(2, $parsedData, "2 items should have been detected in the raw data.");

    $this->assertCount(1, $parsedData[0]['sizes'], "The 'sizes' key should have matched the variation.");
    $this->assertArrayHasKey('sm', $parsedData[0]['sizes'], "The 'sizes' key should have matched the variation.");
    $this->assertEquals('/', $parsedData[0]['path'], "The path key should be correct.");
    $this->assertCount(3, $parsedData[0]['users'], "The 'users' key should have matched the variation.");

    $this->assertCount(2, $parsedData[1]['sizes'], "The 'sizes' key should have matched the default variations.");
    $this->assertCount(1, $parsedData[1]['users'], "The 'users' key should have matched the default variations.");
  }

}
