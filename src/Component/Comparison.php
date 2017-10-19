<?php

/**
 * @file Component responsible for the generation of generate comparison files
 *  based on the different screenshots that we're generated.
 */
namespace surangapg\Haunt\Component;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Comparison {

  /**
   * @var string[]
   */
  protected $folders;

  /**
   * @var Report
   */
  protected $report;

  /**
   * Comparison constructor.
   *
   * @param string[] $folders
   *   An array of folders with files that should be compared. Note that all
   *   The folders should have a current.png and a baseline.png file.
   * @param \Symfony\Component\Console\Output\OutputInterface|NULL $output
   *   Output interface to handle the displaying of the output.
   * @param \surangapg\Haunt\Component\Report $report
   *   Report for this run.
   */
  public function __construct(array $folders, OutputInterface $output = null, Report $report = null) {
    $this->setFolders($folders);

    if (!isset($output)) {
      $output = new BufferedOutput();
    }
    $this->setOutput($output);

    if (!isset($report)) {
      $report = new Report();
    }
    $this->setReport($report);
  }

  /**
   * Make the actual comparisons for all the items in a given list of folders.
   */
  public function compare() {

    // @TODO Validate the existance of the compare imagemagick function here.

    $this->getOutput()->writeln('');
    $this->getOutput()->writeln('<fg=white>Running comparison for images</>');

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

    $this->getOutput()->writeln(sprintf(' Comparing images in %s', $fileDir), OutputInterface::VERBOSITY_NORMAL);

    $baseline = $fileDir . "/baseline.png";
    $current = $fileDir . "/new.png";

    // Validate that the baseline and the current file are equal in size.
    list($baselineWidth, $baselineHeight) = getimagesize($baseline);
    list($currentWidth, $currentHeight) = getimagesize($current);

    $this->getOutput()->writeln(sprintf(' baseline.png: %sx%s', $baselineWidth, $baselineHeight), OutputInterface::VERBOSITY_DEBUG);
    $this->getOutput()->writeln(sprintf(' new.png %sx%s', $currentWidth, $currentHeight), OutputInterface::VERBOSITY_DEBUG);

    if ($baselineWidth != $currentWidth || $baselineHeight != $currentHeight) {

      $this->getOutput()->writeln('<fg=red>Image dimensions do not match, skipping</>');
      $this->getReport()->addError($fileDir, "Image dimensions did not match, skipping");

      return;
    }

    $diff = $fileDir . "/diff.png";
    exec("compare -dissimilarity-threshold 1 -fuzz 0% -metric AE -highlight-color red $baseline $current $diff 2>&1 ", $output);

    // @TODO Add this to the report once it is available.
    $diffPercentage = $this->calcDiffPercentage($output[0], ['width' => $baselineWidth, 'height' => $baselineHeight]);

    $this->getOutput()->writeln(sprintf(' Difference: %s', $this->formatPercentage($diffPercentage)));
    $this->getReport()->addRecord($fileDir, $diffPercentage);
  }

  /**
   * Add a color to an output percentage.
   *
   * @param float $diffPercentage
   *   The difference in percent.
   *
   * @return string
   *   String wrapped in a pretty color.
   */
  protected function formatPercentage(float $diffPercentage) {

    if($diffPercentage < 2) {
      return '<fg=green>' . round($diffPercentage, 2) . '%</>';
    }
    elseif($diffPercentage < 10) {
      return '<fg=yellow>' . round($diffPercentage, 2) . '%</>';
    }
    else {
      return '<fg=red>' . round($diffPercentage, 2) . '%</>';
    }
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

  /**
   * @return \surangapg\Haunt\Component\Report
   */
  public function getReport() {
    return $this->report;
  }

  /**
   * @param $report
   */
  public function setReport(Report $report) {
    $this->report = $report;
  }
}