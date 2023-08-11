<?php

namespace Drupal\flagship_programs\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a programs details block.
 *
 * @Block(
 *   id = "programs_details",
 *   admin_label = @Translation("programs Details"),
 *   category = @Translation("Custom"),
 * )
 */
final class ProgramsDetailsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Contains the ConfigFactory Object for configuration data fetching.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs the plugin instance.
   *
   * @param array $configuration
   *   Contains the Configuration array.
   * @param mixed $plugin_id
   *   Contains the Plugin Id of the Block.
   * @param mixed $plugin_definition
   *   Contains the plugin definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Contains the configuration factory Object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'template' => 'table',
      'limit' => 2,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['template'] = [
      '#type' => 'select',
      '#title' => $this->t('Select template'),
      '#options' => [
        'table' => $this->t('Table'),
        'list' => $this->t('List'),
      ],
      '#default_value' => $this->configuration['template'],
    ];
    $form['limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Limit'),
      '#max' => 4,
      '#default_value' => $this->configuration['limit'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['template'] = $form_state->getValue('template');
    $this->configuration['limit'] = $form_state->getValue('limit');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $config = $this->configFactory->getEditable('flagship_programs.data');
    $limit = (int) $this->configuration['limit'];
    $data = $config->get('data') ? array_slice($config->get('data'), 0, $limit) : [];
    $template = $this->configuration['template'];

    return [
      '#type' => 'theme',
      '#theme' => 'flagship_program__' . $template,
      '#data' => $data,
      '#attached' => [
        'library' => ['simplesmart/global-css'],
      ],
      '#cache' => [
        'tags' => $config->getCacheTags() + $this->getCacheTags(),
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    return AccessResult::allowedIf(TRUE);
  }

}
