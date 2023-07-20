<?php

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides The RSVP main block.
 *
 * @Block(
 *   id = "rsvp_block",
 *   admin_label = @Translation("RSVP Block")
 * )
 */
class RSVPBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Contains the current route information.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $route;

  /**
   * Contains the FormBuiler Object.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Contains the config object.
   * 
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs the RSVP Block dependencies.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   *   Takes the RouteMatch Object.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   Takes the FormBuilder Object.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   Takes the ConfigFactory object.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    RouteMatchInterface $route,
    FormBuilderInterface $form_builder,
    ConfigFactoryInterface $config
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->route = $route;
    $this->formBuilder = $form_builder;
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('form_builder'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Fetching the RSVP form we just made.
    $rsvp_form = $this->formBuilder->getForm('Drupal\rsvplist\Form\RSVPForm');
    return $rsvp_form;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $config_data = $this->config->get('rsvplist.settings')->get('allowed_types');
    $entity_type_label = $this->route->getParameter('node')->type->entity->label();

    // If Viewing the node, get the full node object.
    $node = $this->route->getParameter('node');

    if ($node && in_array(strtolower($entity_type_label), $config_data)) {
      // Checking if the account has permission.
      $has_permission = AccessResult::allowedIfHasPermission($account, 'View RSVP List');
      return $has_permission;
    }

    return AccessResult::forbidden();
  }

}
