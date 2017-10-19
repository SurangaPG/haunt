<?php

/**
 * @file Component responsible for discovery of all the comparable screenshots.
 */
namespace surangapg\Haunt\Component;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Snapshot
 *
 * @package surangapg\Haunt\Component
 */
class Snapshot {

  /**
   * The absolute paths to the url's to make snapshots of.
   *
   * @var string[]
   */
  protected $paths = [];

  /**
   * Resolution for to make the screenshots at.
   *
   * @var int[]
   */
  protected $resolution = [1200, 600];

  /**
   * The target file to use (usually either baseline.png or new.png).
   *
   * @var string
   */
  protected $target;

  /**
   * The output for the command.
   *
   * @var OutputInterface
   */
  protected $output;

  /**
   * The output directory for the file.
   *
   * @var string
   */
  protected $outputDir;

  /**
   * Discovery constructor.
   *
   * @param string[] $paths
   *   Absolute url's for the pages to visit.
   * @param string $target
   *   Target name for the files.
   * @param string $outputDir
   *
   * @param int[]|NULL $resolution
   *   Array of width and height.
   *
   */
  public function __construct(array $paths, string $target, string $outputDir, OutputInterface $output = null, array $resolution = null) {

    $this->setPaths($paths);
    $this->setTarget($target);

    if (isset($resolution)) {
      $this->setResolution($resolution);
    }

    if (!isset($output)) {
      $output = new BufferedOutput();
    }
    $this->setOutput($output);
  }

  /**
   * Make snapshots for all the items locations.
   */
  public function snap() {
    $session = new Session(new Selenium2Driver());
    $session->start();

    $session->resizeWindow($this->resolution['width'], $this->resolution['height']);

    foreach ($this->getPaths() as $index => $path) {

      $this->getOutput()->writeln(sprintf('  Visiting <fg=green>%s</> at <fg=green>%s</>x<fg=green>%s</>', $path, $this->resolution['width'], $this->resolution['height']));
      $session->visit($path);
      $screenShot = $session->getScreenshot();
    }

    $session->stop();
  }

  /**
   * @return \string[]
   */
  public function getPaths() {
    return $this->paths;
  }

  /**
   * @param $paths
   */
  public function setPaths($paths) {
    $this->paths = $paths;
  }

  /**
   * @return \int[]
   */
  public function getResolution() {
    return $this->resolution;
  }

  /**
   * @param $resolution
   */
  public function setResolution($resolution) {
    $this->resolution = $resolution;
  }

  /**
   * @return \Symfony\Component\Console\Output\OutputInterface
   */
  public function getOutput() {
    return $this->output;
  }

  /**
   * @param $output
   */
  public function setOutput(OutputInterface $output) {
    $this->output = $output;
  }

  public function getTarget() {
    return $this->target;
  }

  public function setTarget($target) {
    $this->target = $target;
  }

}