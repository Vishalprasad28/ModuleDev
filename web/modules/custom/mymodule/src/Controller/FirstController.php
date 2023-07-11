<?php

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * @package \Drupal\mymodule\Controller\FirstController
 */
class FirstController extends ControllerBase {

  /**
   * @method simpleContent()
   * 
   * @return array
   */
  public function simpleContent() {
    return [
      '#type' => 'markup',
      '#markup' => t('Hello World i am from the first Controller'),
    ];
  }

  /**
   * @method variableContent()
   * 
   * @param string $name1
   * @param string $name2
   * 
   * @return array
   */
  public function variableContent($name1, $name2) {
    return [
      '#type' => 'markup',
      '#markup' => t('@name1 is a good boy while @name2 is a .....I am here to introduce you with some terms and conditions of our website',
       ['@name1' => $name1, '@name2' => $name2]),
    ];
  }

}
