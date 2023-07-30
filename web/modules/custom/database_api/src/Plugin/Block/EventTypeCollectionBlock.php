<?php declare(strict_types = 1);

namespace Drupal\database_api\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\taxonomy\Entity\Term;
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
   * Contains the EntityTypeManager Object to handle the entities of Drupal.
   * 
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityManager;

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
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->connection = $connection;
    $this->entityManager =$entity_type_manager;
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
      $container->get('entity_type.manager')
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
    $query = $this->connection->select('node_field_data', 'n');
    $query->join('node__field_event_type', 'event_type', 'event_type.entity_id = n.nid');
    $query->addField('event_type', 'field_event_type_target_id', 't_id');
    $query->condition('type', 'events');
    $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

    $array = [];
    // Calculating the count of events of given type.
    foreach ($result as $row) {
      $array[$row['t_id']] = isset($array[$row['t_id']]) ? ++$array[$row['t_id']] : 1;
    }

    $table_rows = [];
    // Forming the associative array of the data to be rendered.
    foreach ($array as $term_id => $count) {
      if (count($table_rows) >= $limit) {
        break;
      }
      $term =$this->entityManager->getStorage('taxonomy_term')->load($term_id);
      if ($term && $term->bundle() == 'events') {
        $temp['desc'] = $this->t('Event of type @type', ['@type' => $term->getName()]);
      }
      else {
        $temp['desc'] = $this->t('undefined');
      }
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
