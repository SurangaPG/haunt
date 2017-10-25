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

  /**
   * @var array
   */
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
   * @param array $info
   *   Additional information to be added to the array.
   */
  public function addRecord(string $folder, float $diffPercent, array $info = []) {

    $this->addDefaultData($info, $folder);

    // @TODO Make the report cleaner by having an add a singular addGroup.
    $this->records[$info['group']['id']]['info'] = $info['group'];

    $this->records[$info['group']['id']]['paths'][] = [
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
   * @param array $info
   *   Additional information to be added to the array.
   */
  public function addError(string $folder, string $error, array $info = []) {

    $this->addDefaultData($info, $folder);

    // @TODO Make the report cleaner by having an add a singular addGroup.
    $this->records[$info['group']['id']]['info'] = $info['group'];

    $this->records[$info['group']['id']][] = [
      'folder' => $folder,
      'error' => $error,
    ];
  }

  /**
   * Get all the data that was collected.
   *
   * @return array
   *   All the collected data.
   */
  public function getData() {
    return [
      'startTime' => $this->startTime,
      // @TODO Make this more representative.
      'endTime' => time(),
      'records' => $this->records,
    ];
  }

  /**
   * Adds default data to the info array.
   *
   * @param array $info
   *  Array with extra info about a record.
   */
  protected function addDefaultData(array &$info, string $folder) {

    if (!isset($info['group']['id'])) {
      $info['group']['id'] = base64_encode(dirname($folder));
    }
  }
}