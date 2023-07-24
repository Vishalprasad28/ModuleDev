<?php

/**
 * @file
 * Contains the Controller code for the simple page.
 */
namespace Drupal\helloworld\Controller;

use Drupal\Core\Controller\ControllerBase;

class SimpleController extends ControllerBase {

  /**
   * @var object
   */
  private $current_user;

  public function __construct() {
    $this->current_user = \Drupal::currentUser();
  }

  /**
   * Function to print sinple message on UI.
   * 
   * @return array
   *   Returns the Render Array to display on the UI.
   */
  public function helloUser() {
    $content = [
      '#type' => 'markup',
      '#markup' => $this->t('Hello <strong>@user</strong> how are you? <br><br> gocha we got your email id: @email',
      [
        '@user' => $this->current_user->getAccountName(),
        '@email' => $this->current_user->getEmail(),
      ]),
      '#cache' => [
        'max-age' => 0
      ],
    ];

    return $content;
  }
}
