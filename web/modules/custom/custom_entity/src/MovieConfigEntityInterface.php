<?php

namespace Drupal\custom_entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface defining a movie entity module entity type.
 */
interface MovieConfigEntityInterface extends ConfigEntityInterface {

  /**
   * Function to get the description value.
   * 
   * @param int $trim_limit
   *   Takes the trim limit value.
   * 
   * @return srring
   *   Returns the description as substring trimmed to maximum limit provided.
   */
  public function getDescription(int $trim_limit);

  /**
   * Function to get the movie release year.
   * 
   * @return int
   *   Returns the release year of the movie.
   */
  public function getReleaseYear();

}
