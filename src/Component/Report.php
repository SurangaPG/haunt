<?php

/**
 * @file Component responsible for discovery of all the comparable screenshots.
 */
namespace surangapg\Haunt\Component;

/**
 * Class Report
 *
 * Basic implementation for a report class that collects all the data about
 * the comparisons.
 *
 * @package surangapg\Haunt\Component
 */
class Report {

  /**
   * @var int the report was generated
   */
  protected $startTime;

  protected $records = [];

  /**
   * Report constructor.
   */
  public function __construct() {

    $this->startTime = time();
  }

  /**
   * Add a single record to the report.
   *
   * @param string $folder
   *   Folder that was validated.
   * @param float $diffPercent
   *   Percentage difference detected.
   */
  public function addRecord(string $folder, float $diffPercent) {
    $this->records[] = [
      'folder' => $folder,
      'diff' => $diffPercent,
    ];
  }

  /**
   * Add a single error record to the report.
   *
   * @param string $folder
   *   Folder that was validated.
   * @param string $error
   *   The error message.
   */
  public function addError(string $folder, string $error) {
    $this->records[] = [
      'folder' => $folder,
      'error' => $error,
    ];
  }

  /**
   *
   */
  public function getData() {
    return [
      'startTime' => $this->startTime,
      // @TODO Make this more representative.
      'endTime' => time(),
      'records' => $this->records,
    ];
  }
}