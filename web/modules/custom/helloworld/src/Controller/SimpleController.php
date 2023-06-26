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
   * To Return a simple markup of user details.
   * 
   * @return array
   */
  public function helloUser() {

    $session = \Drupal::request()->getSession();

    $user_details = $session->get('custom_user.details');
    $content = [
      '#type' => 'markup',
      '#markup' => $this->t('Hello <strong>@user</strong> how are you? <br><br> Your email id: @email <br> Your Phone Number: <strong>@phone</strong> <br> Your Gender: <strong> @gender </strong>',
      [
        '@user' => $user_details['full_name'],
        '@email' => $user_details['email'],
        '@phone' => $user_details['phone'],
        '@gender' => $user_details['gender'],
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
