<?php

namespace Drupal\flagship_programs\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contains the function to render the flagfship program data.
 */
class FlagshipProgramView extends ControllerBase {

  /**
   * Contains the Config Factory object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs the config factory object used to fetch config form data.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Contains the Config Factory object.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Function to render the config form data that has be taken input.
   *
   *   Form used to process data is,
   *
   *   @see \Drupal\flagship_programs\Form\FlagshipProgramsForm
   *
   * @param int $limit
   *   Takes the data limit integer value.
   *
   * @return array
   *   Returns the render array.
   */
  public function viewFlagshipData(int $limit) {
    $config = $this->configFactory->getEditable('flagship_programs.data');
    $data = $config->get('data') ? array_slice($config->get('data'), 0, $limit) : [];
    $template = $config->get('template');

    return [
      '#type' => 'theme',
      '#theme' => 'flagship-program-' . $template . '-view',
      '#data' => $data,
      '#attached' => [
        'library' => ['simplesmart/global-css'],
      ],
      '#cache' => [
        'tags' => $config->getCacheTags(),
      ],
    ];
  }

}
