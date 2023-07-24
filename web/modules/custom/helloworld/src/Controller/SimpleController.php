<?php

/**
 * @file
 * Contains the Controller code for the simple page.
 */
namespace Drupal\helloworld\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SimpleController extends ControllerBase {

  /**
   * Contains the Current Logged In User details.
   * 
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  public function __construct(AccountInterface $user) {
    $this->currentUser = $user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user')
    );
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
        '@user' => $this->currentUser->getAccountName(),
        '@email' => $this->currentUser->getEmail(),
      ]),
      '#cache' => [
        'max-age' => 0
      ],
    ];

    return $content;
  }

}
