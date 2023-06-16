<?php

/**
 * @file
 * Conatinsthe EnablerService Class for the RSVP Module enabling service
 */

namespace Drupal\rsvplist;

use Drupal\core\database\Connection;

class EnablerService {

  protected $database_connection;

  /**
   * Establishing the Database Connection
   */
  public function __construct(Connection $connection) {
    $this->database_connection = $connection;
  }
}
