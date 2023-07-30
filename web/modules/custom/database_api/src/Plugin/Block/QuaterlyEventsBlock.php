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
 * Provides a quaterly events block.
 *
 * @Block(
 *   id = "database_api_quaterly_events",
 *   admin_label = @Translation("Quaterly Events"),
 *   category = @Translation("Custom"),
 * )
 */
final class QuaterlyEventsBlock extends BlockBase implements ContainerFactoryPluginInterface {


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
      '#title' => $this->t('Data Limit to display'),
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
    $query->addField('nfd', 'field_date_value');
    $query->condition('type', 'events');
    $query->addExpression("YEAR(nfd.field_date_value)", 'year');
    $query->addExpression("QUARTER(nfd.field_date_value)", 'quarter');
    $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

    $array = [];
    // Calculating the events count quarterly.
    foreach ($result as $row) {
      $array[$row['year']][$row['quarter']] = isset($array[$row['year']][$row['quarter']]) ? 
        ++$array[$row['year']][$row['quarter']] : 1;
    }

    $table_rows = [];
    // Forming the associative array of data to render.
    foreach ($array as $year => $events) {
      foreach ($events as $quater_no => $count) {
        if (count($table_rows) >= $limit) {
          break 2;
        }
        $temp['desc'] = $this->t('Year @year quarter @quarter', ['@year' => $year, '@quarter' => $quater_no]);
        $temp['data'] = $count;
        array_push($table_rows, $temp);
      }
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
