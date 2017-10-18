<?php

/**
 * @file Component responsible for the generation of generate comparison files
 *  based on the different screenshots that we're generated.
 */
namespace surangapg\Haunt\Component;

use Symfony\Component\Console\Output\OutputInterface;

class Comparison {

  /**
   * @var string[]
   */
  protected $folders;

  /**
   * Comparison constructor.
   *
   * @param string[] $folders
   *   An array of folders with files that should be compared. Note that all
   *   The folders should have a current.png and a baseline.png file.
   */
  public function __construct(array $folders, OutputInterface $output = null) {
    $this->setFolders($folders);
  }

  /**
   * Make the actual comparisons for all the items in a given list of folders.
   */
  public function compare() {

    // @TODO Validate the existance of the compare imagemagick function here.

    foreach ($this->getFolders() as $folder) {
      $this->compareFolder($folder);
    }
  }

  /**
   * Handle the comparison of a directory. This compares the files if they both
   * exist and adds a diff.png file as needed.
   *
   * @param string $fileDir
   *
   * @return string|NULL
   */
  public function compareFolder($fileDir) {

    $baseline = $fileDir . "/baseline.png";
    $current = $fileDir . "/current.png";

    // Validate that the baseline and the current file are equal in size.
    list($baselineWidth, $baselineHeight) = getimagesize($baseline);
    list($currentWidth, $currentHeight) = getimagesize($current);

    if ($baselineWidth != $currentWidth || $baselineHeight != $baselineHeight) {

      // @TODO Add logging for size mismatch as compare doesn't support this.

      return;
    }

    $diff = $fileDir . "/diff.png";
    exec("compare -dissimilarity-threshold 1 -fuzz 0% -metric AE -highlight-color red $baseline $current $diff 2>&1 ", $output);

    // @TODO Add this to the report once it is available.
    $diffPercentage = $this->calcDiffPercentage($output[0], ['width' => $baselineWidth, 'height' => $baselineHeight]);

  }

  /**
   * Calculate percentage.
   *
   * @param $diffPixels
   * @param $sizeInfo
   * @return float
   */
  protected function calcDiffPercentage($diffPixels, $sizeInfo) {
    return round($diffPixels / ($sizeInfo["width"] * $sizeInfo["height"]) * 100, 2);
  }

  /**
   * @return \string[]
   */
  public function getFolders() {
    return $this->folders;
  }

  /**
   * @param $folders
   */
  public function setFolders(array $folders) {
    $this->folders = $folders;
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