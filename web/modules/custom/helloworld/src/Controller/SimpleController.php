<?php

/**
 * @file
 * Contains the Controller code for the simple page
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
   * @method
   * To Return a simple markup.
   * 
   * @return array
   */
  public function helloUser() {
    $content = [
      '#type' => 'markup',
      '#markup' => $this->t('Hello <strong>@user</strong> how are you? <br><br> gocha we got your email id: @email',
      [
        '@user' => $this->current_user->getAccountName(),
        '@email' => $this->current_user->getEmail(),
      ]),
    ];

    return $content;
  }

  /**
   * @method
   * To Return a simple markup.
   * 
   * @return array
   */
  public function dynamicGreeting(string $name, string $company) {
    $content = [
      '#type' => 'markup',
      '#markup' => t('Hello <strong> @user </strong> You are an employee at <strong> @company </strong>',
       [
        '@user' => $name,
        '@company' => $company,
       ]
       ),
      ];

    return $content;
  }
}