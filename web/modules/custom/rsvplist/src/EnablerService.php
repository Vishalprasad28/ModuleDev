<?php

namespace Drupal\rsvplist;

use Drupal\Core\database\Connection;
use Drupal\node\Entity\Node;

/**
 *
 */
class EnablerService {

  protected $database_connection;

  /**
   *
   */
  public function __construct(Connection $connection) {
    $this->database_connection = $connection;
  }

  /**
   * @method isEnabled()
   * Checks if the Current node is RSVP Enabled or not.
   *
   * @param \Drupal\node\Entity\Node $node
   * @return bool
   *   Returns True or False Based on the RSVP Enabled status
   */
  public function isEnabled(Node $node) {

    if ($node->isNew()) {
      return FALSE;
    }

    try {
      $rsvp_enabled = $this->database_connection->select('rsvplist_enabled', 're');
      $rsvp_enabled->fields('re', ['nid']);
      $rsvp_enabled->condition('nid', $node->id());
      $result = $rsvp_enabled->execute();

      return !(empty($result->fetchCol()));

    }
    catch (\Exception $e) {
      \Drupal::messenger()->addMessage(t("Sorry Drupal Couldn't Connect to database"));
      return NULL;
    }
  }

  /**
   * @method setEnabled()
   *
   * Enables the RSVP List Service for a particular node
   *
   * @param \Drupal\node\Entity\Node $node
   * @throw Exception
   */
  public function setEnabled(Node $node) {

    try {
      if (!$this->isEnabled($node)) {
        $insert = $this->database_connection->insert('rsvplist_enabled');
        $insert->fields(['nid']);
        $insert->values([$node->id()]);
        $insert->execute();
      }
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addMessage(t("Sorry Could't Connect to the database"));
      return $e;
    }

  }

  /**
   * @method deleteEnabled()
   *
   * Deletes the RSVP List EnBled Node ids from the table
   *
   * @param \Drupal\node\Entity\Node $node
   */
  public function deleteEnabled(Node $node) {

    try {
      $delete = $this->database_connection->delete('rsvplist_enabled');
      $delete->condition('nid', [$node->id()]);
      $delete->execute();
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addMessage(t("Sorry Could't Connect to the database"));
    }
  }

}
