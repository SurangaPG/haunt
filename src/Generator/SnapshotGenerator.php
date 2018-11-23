<?php

/**
 * @file Component responsible for discovery of all the comparable screenshots.
 */
namespace surangapg\Haunt\Generator;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use surangapg\Haunt\Manifest\Item\ManifestItemInterface;
use surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface;
use surangapg\Haunt\Manifest\ManifestInterface;
use surangapg\Haunt\Output\Structure\OutputStructureInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Snapshot
 *
 * @package surangapg\Haunt\Component
 */
class SnapshotGenerator {

  /**
   * The manifest to get the data from.
   *
   * @var \surangapg\Haunt\Manifest\ManifestInterface
   *   The manifest with all the data for the screenshots.
   */
  protected $manifest;

  /**
   * The structure for the output files.
   *
   * @var \surangapg\Haunt\Output\Structure\OutputStructureInterface
   *   The output data structure for the screenshot.
   */
  protected $outputStructure;

  /**
   * The output writer.
   *
   * @var \Symfony\Component\Console\Output\BufferedOutput|\Symfony\Component\Console\Output\OutputInterface
   *   The output item.
   */
  protected $output;

  /**
   * Get the session driver.
   *
   * @var \Behat\Mink\Session
   *   Session for the driver.
   */
  protected $session;

  /**
   * File system helper.
   *
   * @var Filesystem
   *   File system handler.
   */
  protected $fs;

  /**
   * The basic domain to visit.
   *
   * @var string
   *   The base url for the host.
   */
  protected $baseUrl;

  /**
   * Snapshot constructor.
   */
  public function __construct(ManifestInterface $manifest, OutputStructureInterface $outputStructure, OutputInterface $output = NULL) {
    if (!isset($output)) {
      $output = new BufferedOutput();
    }

    $this->output = $output;
    $this->manifest = $manifest;
    $this->outputStructure = $outputStructure;
  }

  /**
   * Generate all the screenshots.
   *
   * @param string $baseUrl
   *   The base url to visit.
   */
  public function generate(string $baseUrl, $browserName = 'firefox', $desiredCapabilities = NULL, $wdHost = 'http://localhost:4444/wd/hub') {

    $this->session = new Session(new Selenium2Driver($browserName, $desiredCapabilities, $wdHost));
    $this->session->start();

    $this->fs = new Filesystem();
    $this->baseUrl = rtrim($baseUrl, '/') . '/';

    foreach ($this->manifest->listManifestItems() as $item) {
      $this->handleManifestItem($item);
    }
  }

  /**
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemInterface $item
   */
  protected function handleManifestItem(ManifestItemInterface $item) {
    foreach ($item->listVariations() as $variation) {
      $this->handleManifestItemVariation($variation);
    }
  }

  /**
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface $variation
   */
  protected function handleManifestItemVariation(ManifestItemVariationLineInterface $variation) {
    $resolution = $variation->getSizeInfo();
    $this->session->resizeWindow($resolution['width'], $resolution['height']);

    $this->session->visit($this->baseUrl . $variation->getUri());

    $screenShot = $this->session->getScreenshot();
    $outputFileName = $this->outputStructure->generateOutputName($variation);

    $this->fs->dumpFile($outputFileName, $screenShot);

    passthru('convert ' . $outputFileName . ' -gravity north-west  -extent ' . $resolution['width'] . 'x' . $resolution['height'] . ' ' . $outputFileName);
  }

}
