<?php declare(strict_types = 1);

namespace Drupal\database_api\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a yearly events block.
 *
 * @Block(
 *   id = "database_api_yearly_events",
 *   admin_label = @Translation("Yearly Events"),
 *   category = @Translation("Custom"),
 * )
 */
final class YearlyEventsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Contains the Entity Database Connection Object to manage the entities.
   * 
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * Constructs the plugin instance.
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
      'limit' => 1,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Max Limit of Data'),
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
      'info_header' => $this->t('Event Year'),
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
        'tags' => $this->getCacheTags() + ['node_list:events'],
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
    $query = $this->connection->select('node__field_date', 'nfd');
    $query->join('node_field_data', 'n_data', 'nfd.entity_id = n_data.nid');
    $query->addField('n_data', 'title');
    $query->condition('type', 'events');
    $query->addExpression("YEAR(nfd.field_date_value)", 'year');
    $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    $array = [];
    foreach ($result as $row) {
      $array[$row['year']][] = $row['title'];
    }
    $table_rows = [];
    foreach ($array as $year => $events) {
      if (count($table_rows) >= $limit) {
        break;
      }
      $temp['desc'] = $this->t('Event count of year @year', ['@year' => $year]);
      $temp['data'] = count($events);
      array_push($table_rows, $temp);
    }
    return $table_rows;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIf(TRUE);
  }

}
