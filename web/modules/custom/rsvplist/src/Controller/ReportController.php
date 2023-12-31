<?php

namespace Drupal\rsvplist\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\database\Connection;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ReportController class to process display the RSVP submitted data entries.
 */
class ReportController extends ControllerBase {

  /**
   * A database connection object.
   *
   * @var \Drupal\Core\database\Connection
   */
  protected $databaseConnection;

  /**
   * A Messenger object.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs the Dependencies for this controller.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   Takes the Messenger object.
   * @param \Drupal\Core\database\Connection $connection
   *   Takes the database Connection object.
   */
  public function __construct(MessengerInterface $messenger, Connection $connection) {
    $this->messenger = $messenger;
    $this->databaseConnection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('messenger'),
      $container->get('database'),
    );
  }

  /**
   * Function to load the RSVP submission datas.
   *
   * It includes the user name of the user submitting the form,
   * Node id of the node,
   * and the email id the form was submitted with.
   *
   * @return array|null
   *   Returns the array or Null.
   */
  protected function load() {
    try {
      $database = $this->databaseConnection;
      $query = $database->select('rsvplist', 'r');
      // Joining the User's table to get the information about the user's
      // username.
      $query->join('users_field_data', 'u', 'r.uid = u.uid');
      // Joining with the node table to get the information about the Event's
      // name.
      $query->join('node_field_data', 'n', 'r.nid = n.nid');

      // Add the Fields to be displayed.
      $query->addField('u', 'name', 'username');
      $query->addField('n', 'title');
      $query->addField('r', 'mail');

      // Executing the query and fetching the result.
      $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

      return $result;
    }
    catch (\Exception $e) {
      $this->messenger->addMessage($this->t("Sorry Data Couldn't be loaded"));
      return NULL;
    }
  }

  /**
   * Function to Return an render array of all the RSVP Reports.
   *
   * @return array|null
   *   Returns the array or Null.
   */
  public function report() {
    $content = [];
    $content['message'] = [
      '#markup' => $this->t('Below is the List of all the RSVP Reports'),
    ];
    $headers = [
      $this->t('Username'),
      $this->t('Event'),
      $this->t('Email'),
    ];

    // Getting the Table Data from the database.
    $table_rows = $this->load();

    $content['table'] = [
      '#type' => 'table',
      '#header' => $headers,
      '#rows' => $table_rows,
      '#empty' => $this->t('No Entries Available'),
    ];

    // Setting up the cachebility of the table data.
    // We are setting it up to 0 because we want to display the most up to date
    // data.
    $content['#cache']['max-age'] = 0;

    // Returning the Render Array to be rendered.
    return $content;
  }

}
