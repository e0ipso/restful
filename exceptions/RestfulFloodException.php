<?php

/**
 * @file
 * Contains RestfulFloodException
 */

class RestfulFloodException extends RestfulException {

  /**
   * Defines the HTTP error code.
   *
   * @var int
   */
  protected $code = 429;

  /**
   * Defines the description.
   *
   * @var string
   */
  protected $description = 'Too Many Requests.';
}
