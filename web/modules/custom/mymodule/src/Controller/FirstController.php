<?php

/**
 * @file
 * Generates Markup to be displayed Functions is this controller
 * is Wired to Drupal in mymodule.routing.yml.
 */

namespace Drupal\mymodule\Controller;

use Drupal\Core\Controller\ControllerBase;

class FirstController extends ControllerBase {
  public function simpleContent() {
    return [
      '#type' => 'markup',
      '#markup' => t('Hello World i am from the first Controller'),
    ];
  }
  public function variableContent($name1, $name2) {
    return [
      '#type' => 'markup',
      '#markup' => t('@name1 is a good boy while @name2 is a .....I am here to introduce you with some terms and conditions of our website', 
       ['@name1' => $name1, '@name2' => $name2]),
    ];
  }
}