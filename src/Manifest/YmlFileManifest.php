<?php

namespace surangapg\Haunt\Manifest;

use surangapg\Haunt\Exception\InvalidManifestConfigException;
use surangapg\Haunt\Manifest\Item\ManifestItem;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YmlFileManifest
 *
 * Reads in the data from a yaml file. Allowing it to be stored on the file
 * system.
 */
class YmlFileManifest implements ManifestInterface {

  /**
   * Construction time config.
   *
   * @var array
   *   The raw config for the manifest.
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public function listManifestItems() {

    $items = [];
    foreach ($this->parseData($this->readFile()) as $item) {
      $items[] = new ManifestItem($item);
    }

    return $items;
  }

  /**
   * {@inheritdoc}
   */
  public function checkConfig() {
    if (!isset($this->config['file']) || !is_string($this->config['file'])) {
      throw new InvalidManifestConfigException('The config key "file" is required and should be a string.');
    }

    if (!file_exists($this->config['file']) || !is_readable($this->config['file'])) {
      throw new InvalidManifestConfigException(sprintf('The file %s was not found or could not be read.', $this->config['file']));
    }
  }

  /**
   * Parse in the data from the yml file.
   *
   * @param array $rawData
   *   The raw data for the items.
   *
   * @throws \surangapg\Haunt\Exception\InvalidManifestConfigException
   *   If the data could not be read.
   * @throws \Symfony\Component\Yaml\Exception\ParseException
   *   If the yaml data was not correct.
   *
   * @return array
   *   The data in completed chunks.
   */
  public function parseData(array $rawData) {
    $this->checkConfig();

    $parsedData = [];

    return $parsedData;
  }

  /**
   * Parse in the data from the yml file.
   *
   * @throws \Symfony\Component\Yaml\Exception\ParseException
   *   If the yaml data was not correct.
   *
   * @return array
   *   The data in the file.
   */
  public function readFile() {
    return Yaml::parse(file_get_contents($this->config['file']));
  }
}
