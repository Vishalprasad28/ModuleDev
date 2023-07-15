<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Created a controller for testing.
 */
class FirstController extends ControllerBase {

  /**
   * A Simple Controller.
   *
   * @return array
   *   Returns the render array.
   */
  public function simpleContent() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Hello World i am from the first Controller'),
    ];
  }

  /**
   * Function to render the variable content.
   *
   * @param string $name1
   *   A String that comes from the url parameter.
   * @param string $name2
   *   A String that comes from the url parameter.
   *
   * @return array
   *   Returns the render array.
   */
  public function variableContent($name1, $name2) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('@name1 is a good boy while @name2 is a .....I 
         am here to introduce you with some terms and conditions of our website',
         ['@name1' => $name1, '@name2' => $name2]),
    ];
  }

}
