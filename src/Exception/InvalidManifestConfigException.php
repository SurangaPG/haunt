<?php

namespace surangapg\Haunt\Exception;

use Throwable;

/**
 * Class InvalidManifestConfigException
 *
 * Thrown when the information in a manifest interface could not be validated.
 *
 * @package surangapg\Haunt\Exception
 */
class InvalidManifestConfigException extends \Exception {

  /**
   * InvalidManifestConfigException constructor.
   *
   * @param string $message
   *   The message to give.
   * @param int $code
   *   The code for the error.
   * @param \Throwable|NULL $previous
   *   Any previous error.
   */
  public function __construct(string $message = "", int $code = 0, \Throwable $previous = NULL) {
    $message += ' Configuration was invalid: ';

    parent::__construct($message, $code, $previous);
  }

}
