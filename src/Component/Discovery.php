<?php

/**
 * @file Component responsible for discovery of all the comparable screenshots.
 */
namespace surangapg\Haunt\Component;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Discovery
 *
 * @TODO Use the a less custom component (symfony finder perhaps?)
 *
 * @package surangapg\Haunt\Component
 */
class Discovery {

  /**
   * The absolute path to the directory to search in.
   *
   * @var string
   */
  protected $rootDir;

  /**
   * Unix timestamp representing the changed date.
   *
   * Any file not changed after this date will not be validated again.
   * This makes it possible to only check a given set of files.
   *
   * @var int
   */
  protected $changedSince;

  /**
   * The pattern to search for the screenshots.
   *
   * @var string
   */
  protected $pattern = '*/*';

  /**
   * Output interface to handle any output from.
   *
   * @var OutputInterface
   */
  protected $output;

  /**
   * Discovery constructor.
   *
   * @param string $rootDir
   *   The absolute path to the directory to search in.
   * @param int|NULL $changedSince
   *   Only include files that have been changed since a given timestamp.
   * @param string $pattern
   *   The pattern to handle the find the png files with.
   * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
   *   Output interface to handle the displaying of the output.
   */
  public function __construct(string $rootDir, int $changedSince = null, string $pattern = null, OutputInterface $output = null) {

    $this->setRootDir($rootDir);
    $this->setChangedSince($changedSince);

    if (isset($pattern)) {
      $this->setPattern($pattern);
    }

    if (!isset($output)) {
      $output = new BufferedOutput();
    }
    $this->setOutput($output);
  }

  /**
   * Finds all the valid folders that can be compared.
   */
  public function discover() {

    if (!file_exists($this->getRootDir())) {
      throw new \Exception(sprintf("The directory %s could not be found.", $this->getRootDir()));
    }

    $folders = $this->discoverFolders();

    $this->getOutput()->writeln('');
    $this->getOutput()->writeln('<fg=white>Validating discovered directories</>');
    $folders = array_filter($folders, [$this, 'validateFolder']);

    return $folders;
  }

  /**
   * Check or a folder passes all the criteria for comparison.
   *
   * @param string $folder
   *  The folder to validate.
   *
   * @return bool
   *   Flag indicating or the folder is valid.
   */
  protected function validateFolder(string $folder) {

    $passed = TRUE;

    $this->getOutput()->write(sprintf('Validating %s', $folder), OutputInterface::VERBOSITY_NORMAL);

    // A baseline file should exist or the folder can be passed over.
    if (!file_exists($folder . '/baseline.png')) {
      $passed = FALSE;
      $this->getOutput()->write('  No baseline.png found in folder', OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    // A "current" file needs to exist or the folder can be passed over.
    if (!file_exists($folder . '/current.png')) {
      $passed = FALSE;
      $this->getOutput()->write('  No current.png found in folder', OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    $treshHold = $this->getChangedSince();

    // If a treshold was added the folder has to have been changed since then.
    if (isset($treshHold)) {
      $changedTime = filectime($folder);

      if (!$changedTime || $changedTime < $treshHold) {
        $passed = FALSE;
        $this->getOutput()->write('  No changes since the given treshold.', OutputInterface::VERBOSITY_VERY_VERBOSE);
      }
    }

    if ($passed) {
      $this->getOutput()->write('  <fg=green>Passed</>', OutputInterface::VERBOSITY_NORMAL);
    }
    else {
      $this->getOutput()->write('  <fg=red>Failed</>', OutputInterface::VERBOSITY_NORMAL);
    }

    return $passed;
  }

  /**
   * Get an array of all the folders that contain images to compare.
   * @return array
   */
  protected function discoverFolders() {

    $this->getOutput()->writeln('<fg=white>Discovering directories</>');
    $this->getOutput()->writeln(sprintf('Searching in <fg=yellow>%s</>', $this->getRootDir()), OutputInterface::VERBOSITY_NORMAL);

    $dirs = glob($this->getRootDir() . $this->getPattern(), GLOB_ONLYDIR);

    $this->getOutput()->writeln([
      sprintf('Matching pattern: <fg=yellow>%s</> ', $this->getPattern()),
      sprintf('Detected folders: <fg=green>%s</>', count($dirs)),
    ], OutputInterface::VERBOSITY_NORMAL);

    $this->getOutput()->writeln('', OutputInterface::VERBOSITY_VERY_VERBOSE);
    $this->getOutput()->writeln($dirs, OutputInterface::VERBOSITY_VERY_VERBOSE);

    return $dirs;
  }

  /**
   * @return string
   */
  public function getPattern() {
    return $this->pattern;
  }

  /**
   * @param $pattern
   */
  public function setPattern($pattern) {
    $this->pattern = $pattern;
  }

  /**
   * @return int
   */
  public function getChangedSince() {
    return $this->changedSince;
  }

  /**
   * @param $changedSince
   */
  public function setChangedSince($changedSince) {
    $this->changedSince = $changedSince;
  }

  /**
   * @return string
   */
  public function getRootDir() {
    return $this->rootDir;
  }

  /**
   * @param string $rootDir
   */
  public function setRootDir(string $rootDir) {
    $this->rootDir = rtrim($rootDir, '/') . '/';
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

}