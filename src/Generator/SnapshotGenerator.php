<?php

/**
 * @file Component responsible for discovery of all the comparable screenshots.
 */
namespace surangapg\Haunt\Generator;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface;
use surangapg\Haunt\Manifest\ManifestInterface;
use surangapg\Haunt\Output\Structure\OutputStructureInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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
   * SnapshotGenerator constructor.
   *
   * @param \surangapg\Haunt\Manifest\ManifestInterface $manifest
   *   The manifest with all the expected items.
   * @param \surangapg\Haunt\Output\Structure\OutputStructureInterface $outputStructure
   *   The structure for the output.
   * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
   *   The output item to provide output to the user.
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
   * @param array $metaInfo
   *   Array with some information about the meta data for this run.
   * @param string $browserName
   *   Name for the browser to use (defaults to firefox).
   * @param array|null $desiredCapabilities
   *   All the capabilities for the driver.
   * @param string $wdHost
   *   The host for the driver.
   */
  public function generate(string $baseUrl, array $metaInfo = [], $browserName = 'firefox', $desiredCapabilities = NULL, $wdHost = 'http://localhost:4444/wd/hub') {

    $this->session = new Session(new Selenium2Driver($browserName, $desiredCapabilities, $wdHost));
    $this->session->start();

    $this->fs = new Filesystem();
    $this->baseUrl = rtrim($baseUrl, '/') . '/';

    $this->getOutput()->writeln('<fg=yellow>Generating snapshots</>');
    $this->getOutput()->writeln(sprintf(' Found <fg=white>%s</> items in the manifest', count($this->manifest->listManifestItems())));
    foreach ($this->manifest->listManifestItems() as $item) {
      $this->getOutput()->writeln(sprintf('   Checking <fg=white>%s</> - %s variations', $item->getUri(), count($item->listVariations())));
      foreach ($item->listVariations() as $variation) {
        $this->handleManifestItemVariation($variation);
      }
    }

    // Write out a small meta file.
    $metaInfo['domain'] = $this->baseUrl;
    $this->fs->dumpFile($this->outputStructure->getFolderRoot() . '/meta.yml', Yaml::dump($metaInfo));
  }

  /**
   * Handle a single variation for a manifest item.
   *
   * @param \surangapg\Haunt\Manifest\Item\ManifestItemVariationLineInterface $variation
   *   The variation to handle.
   */
  protected function handleManifestItemVariation(ManifestItemVariationLineInterface $variation) {
    $resolution = $variation->getSizeInfo();
    $this->session->resizeWindow($resolution['width'], $resolution['height']);

    $this->session->visit($this->baseUrl . $variation->getUri());

    $screenShot = $this->session->getScreenshot();
    $outputFileName = $this->outputStructure->generateOutputName($variation);

    $this->fs->dumpFile($outputFileName, $screenShot);

    $this->getOutput()->writeln(sprintf('    - Checking: %s at %sx%s', $variation->getVisitor(), $resolution['width'], $resolution['height']));
    passthru('convert ' . $outputFileName . ' -gravity north-west  -extent ' . $resolution['width'] . 'x' . $resolution['height'] . ' ' . $outputFileName);
  }

  /**
   * Get the output interface.
   *
   * @return \Symfony\Component\Console\Output\BufferedOutput|\Symfony\Component\Console\Output\OutputInterface
   *   Output interface being used.
   */
  public function getOutput() {
    return $this->output;
  }

}
