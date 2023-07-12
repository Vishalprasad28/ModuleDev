<?php

namespace Drupal\rsvplist;

use Drupal;
use Drupal\Core\database\Connection;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\node\Entity\Node;
use Exception;

/**
 * EnablerService Class.
 */
class EnablerService {

  /**
   * @var Connection $databaseConnection
   */
  protected Connection $databaseConnection;

  /**
   * @var MessengerInterface $this->messenger
   */
  protected MessengerInterface $messenger;

  /**
   * @param Drupal\Core\database\Connection $connection
   *   Takes the Connection object.
   * @param Drupal\Core\Messenger\MessengerInterface $messenger
   *   Takes the Messenger Service Object.
   */
  public function __construct(Connection $connection, MessengerInterface $messenger) {
    $this->databaseConnection = $connection;
    $this->messenger = $messenger;
  }

  /**
   * Checks if the Current node is RSVP Enabled or not.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Takes the Node Object.
   * 
   * @return bool
   *   Returns True or False Based on the RSVP Enabled status.
   */
  public function isEnabled(Node $node) {

    if ($node->isNew()) {
      return FALSE;
    }

    try {
      $rsvp_enabled = $this->databaseConnection->select('rsvplist_enabled', 're');
      $rsvp_enabled->fields('re', ['nid']);
      $rsvp_enabled->condition('nid', $node->id());
      $result = $rsvp_enabled->execute();

      return !(empty($result->fetchCol()));

    }
    catch (Exception $e) {
      $this->messenger->addMessage(t("Sorry Drupal Couldn't Connect to database"));
      return NULL;
    }
  }

  /**
   * Enables the RSVP List Service for a particular node.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Takes the Node Object.
   * 
   * @throw Exception.
   */
  public function setEnabled(Node $node) {

    try {
      if (!$this->isEnabled($node)) {
        $insert = $this->databaseConnection->insert('rsvplist_enabled');
        $insert->fields(['nid']);
        $insert->values([$node->id()]);
        $insert->execute();
      }
    }
    catch (Exception $e) {
      $this->messenger->addMessage(t("Sorry Could't Connect to the database"));
      return $e;
    }

  }

  /**
   * Deletes the RSVP List EnBled Node ids from the table.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Takes the Node Object.
   */
  public function deleteEnabled(Node $node) {

    try {
      $delete = $this->databaseConnection->delete('rsvplist_enabled');
      $delete->condition('nid', [$node->id()]);
      $delete->execute();
    }
    catch (Exception $e) {
      $this->messenger->addMessage(t("Sorry Could't Connect to the database"));
    }
  }

}
