<?php

namespace Drupal\custom_entity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\custom_entity\MovieConfigEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MoviePreviewPage extends ControllerBase {

  /**
   * Contains the EntityManager Object to manage the different entities accross
   * the site.
   * 
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityManager;

  /**
   * Constructs the EntityTypeManager Object for managing entities.
   * 
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   Contains the EntityTypeManager object.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }
  
  public function listMovies(MovieConfigEntityInterface $movie_config_entity) {
    $movie_ids = explode(',', $movie_config_entity->get('movies'));
    $nodes = $this->entityManager->getStorage('node')->loadMultiple($movie_ids);

    $datas = [];
    // Storing the Movie datas in an array for rendring.
    foreach ($nodes as $id => $node) {
      $data = [
        'title' => $node->getTitle(),
        'description' => $node->get('body')->value,
        'price' => $node->get('field_movie_price')->value,
        'id' => $id,
      ];
      array_push($datas, $data);
    }

    return [
      '#theme' => 'movie_theme_hook',
      '#year' => $movie_config_entity->get('release_year'),
      '#data' => $datas,
      '#cache' => [
        'tags' => $movie_config_entity->getCacheTags(),
      ]
    ];
  }

}
