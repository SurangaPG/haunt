<?php

/**
 * @file Component responsible for discovery of all the comparable screenshots.
 */
namespace surangapg\Haunt\Component;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

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
  protected $resolution = ['width' => 1200, 'height' => 600];

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
   * @var array
   */
  protected $groupInfo;

  /**
   * Discovery constructor.
   *
   * @param string[] $paths
   *   Absolute url's for the pages to visit.
   * @param string $target
   *   Target name for the files.
   * @param string $outputDir
   *   The directory to save the data to.
   * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
   *   Output interface to handle the messages.
   * @param string[] $groupInfo
   *   Extra information for the snapshot set.
   * @param int[]|NULL $resolution
   *   Array of width and height.
   */
  public function __construct(array $paths, string $target, string $outputDir, OutputInterface $output = null, array $groupInfo = [],  array $resolution = null) {

    $this->setPaths($paths);
    $this->setTarget($target);
    $this->setOutputDir($outputDir);

    if (isset($resolution)) {
      $this->setResolution($resolution);
    }

    if (!isset($output)) {
      $output = new BufferedOutput();
    }
    $this->setOutput($output);

    $this->setGroupInfo($groupInfo);
}

  /**
   * Make snapshots for all the items locations.
   */
  public function snap() {
    $session = new Session(new Selenium2Driver());
    $session->start();

    $fs = new Filesystem();

    $groupInfo = $this->getGroupInfo();

    if (!empty($groupInfo)) {
      $fs->dumpFile($this->outputDir . '/_haunt-info.yml', Yaml::dump(['group' => $groupInfo]));
    }

    // Add group meta information.
    $session->resizeWindow($this->resolution['width'], $this->resolution['height']);
    foreach ($this->getPaths() as $index => $path) {

      $this->getOutput()->writeln(sprintf('  Visiting <fg=green>%s</> at <fg=green>%s</>x<fg=green>%s</>', $path, $this->resolution['width'], $this->resolution['height']));
      $session->visit($path);
      $screenShot = $session->getScreenshot();

      $folder = $this->outputDir .  $index;
      $fs->mkdir($folder);
      $fileName = $folder . '/' . $this->getTarget();

      $this->getOutput()->writeln('    Making snapshot', OutputInterface::VERBOSITY_VERY_VERBOSE);
      file_put_contents($folder . '/' . $this->getTarget(), $screenShot);
      passthru('convert ' . $fileName . ' -gravity north-west  -extent ' . $this->resolution['width'] . 'x' . $this->resolution['height'] . ' ' . $fileName);

      // Add meta information.
      $this->getOutput()->writeln('    Writing meta info', OutputInterface::VERBOSITY_VERY_VERBOSE);
      $metaFileName = $folder . '/_haunt-info.yml';
      $metaInfo = [
        'url' => $path,
      ];
      $fs->dumpFile($metaFileName, Yaml::dump($metaInfo));
    }

    $session->stop();
  }

  /**
   * @param string $outputDir
   */
  public function setOutputDir(string $outputDir) {
    $this->outputDir = rtrim($outputDir, '/') . '/';
  }

  /**
   * @return string
   */
  public function getOutputDir() {
    return $this->outputDir;
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

  /**
   * @return string
   */
  public function getTarget() {
    return $this->target;
  }

  /**
   * @param string $target
   */
  public function setTarget(string $target) {
    $this->target = $target;
  }

  /**
   * @return array
   */
  public function getGroupInfo() {
    return $this->groupInfo;
  }

  /**
   * @param array $groupInfo
   */
  public function setGroupInfo(array $groupInfo) {
    $this->groupInfo = $groupInfo;
  }
}