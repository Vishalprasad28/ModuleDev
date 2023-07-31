<?php

namespace Drupal\database_api\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an event type collection block.
 *
 * @Block(
 *   id = "database_api_event_type_collection",
 *   admin_label = @Translation("Event Type Collection"),
 *   category = @Translation("Custom"),
 * )
 */
final class EventTypeCollectionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Contains the Entity Database Connection Object to manage the entities.
   * 
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructs the plugin instance.
   * 
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Database\Connection $connection
   *   Contains the Connection object for database handling.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Connection $connection,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'limit' => 5,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Limit of data to be displayed'),
      '#default_value' => $this->configuration['limit'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['limit'] = $form_state->getValue('limit');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $rows = $this->fetchNodes($this->configuration['limit']);
    $headers = [
      'info_header' => $this->t("Year's quarter"),
      'data_header' => $this->t('Number of Events'),
    ];

    $build['table'] = [
      '#theme' => 'event_template',
      '#headers' => $headers,
      '#rows' => $rows,
      '#attached' => [
        'library' => [
          'database_api/event_table_style',
        ],
      ],
      '#cache' => [
        'tags' => $this->getCacheTags() + ['node_list:events'] + ['taxonomy_term_list:events'],
      ],
    ];

    return $build;
  }

  /**
   * Function to fetch the events nodes group by year.
   * 
   * @param string $limit
   *   Limit of the data to be fetched.
   * 
   * @return void
   */
  private function fetchNodes(string $limit) {
    try {
      $query = $this->connection->select('node_field_data', 'n');
      $query->join('node__field_event_type', 'event_type', 'event_type.entity_id = n.nid');
      $query->join('taxonomy_term_field_data',  'td', 'td.tid = event_type. field_event_type_target_id');
      $query->addField('td', 'name');
      $query->condition('type', 'events');
      $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
    catch (\Exception $e) {
      $this->messenger()->addMessage('Something wrong happened');
    }

    $array = [];
    // Calculating the count of events of given type.
    foreach ($result as $row) {
      $array[$row['name']] = isset($array[$row['name']]) ? ++$array[$row['name']] : 1;
    }

    $table_rows = [];
    // Forming the associative array of the data to be rendered.
    foreach ($array as $term => $count) {
      if (count($table_rows) >= $limit) {
        break;
      }
      $temp['desc'] = $this->t('Event of type @type', ['@type' => $term]);
      $temp['data'] = $count;
      array_push($table_rows, $temp);
    }

    return $table_rows;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    // @todo Evaluate the access condition here.
    return AccessResult::allowedIf(TRUE);
  }

}
