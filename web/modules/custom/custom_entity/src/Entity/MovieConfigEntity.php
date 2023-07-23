<?php declare(strict_types = 1);

namespace Drupal\custom_entity\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\custom_entity\MovieConfigEntityInterface;

/**
 * Defines the movie entity module entity type.
 *
 * @ConfigEntityType(
 *   id = "movie_config_entity",
 *   label = @Translation("Movie Entity module"),
 *   label_collection = @Translation("Movie Entity modules"),
 *   label_singular = @Translation("movie entity module"),
 *   label_plural = @Translation("movie entity modules"),
 *   label_count = @PluralTranslation(
 *     singular = "@count movie entity module",
 *     plural = "@count movie entity modules",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\custom_entity\MovieConfigEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\custom_entity\Form\MovieConfigEntityForm",
 *       "edit" = "Drupal\custom_entity\Form\MovieConfigEntityForm",
 *       "delete" = "Drupal\custom_entity\Form\MovieConfigEntityDeleteForm",
 *     },
 *   },
 *   config_prefix = "movie_config_entity",
 *   admin_permission = "administer movie_config_entity",
 *   links = {
 *     "collection" = "/admin/structure/movie-config-entity",
 *     "add-form" = "/admin/structure/movie-config-entity/add",
 *     "edit-form" = "/admin/structure/movie-config-entity/{movie_config_entity}",
 *     "delete-form" = "/admin/structure/movie-config-entity/{movie_config_entity}/delete",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "releaseYear" = "release_year",
 *     "movies" = "movies"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "release_year",
 *     "movies"
 *   },
 * )
 */
final class MovieConfigEntity extends ConfigEntityBase implements MovieConfigEntityInterface {

  /**
   * The Entity ID.
   * 
   * @var string
   */
  protected $id;

  /**
   * The Entity label.
   * 
   * @var string
   */
  protected $label;

  /**
   * The Entity description.
   * 
   * @var string
   */
  protected $description;

  /**
   * Release year value of the movie.
   * 
   * @var int
   */
  protected $releaseYear;

  /**
   * Contains the movie titles of the movies.
   * 
   * @var string
   */
  protected $movies;

  /**
   * {@inheritdoc}
   */
  public function getDescription(int $trim_limit) {
    return strlen($this->description) > $trim_limit ? substr($this->description, 0, $trim_limit) . '...' : $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function getReleaseYear() {
    return $this->releaseYear;
  }

}
