<?php

namespace Drupal\helloworld\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A SimpleController class to handle the requests coming from different routes.
 */
class SimpleController extends ControllerBase {

  /**
   * Contains the Current Logged In User details.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs the Current User detailobject.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   Contains the AccountInterface Object, containing the current logged in
   *   user details.
   */
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
        'contexts' => ['user'],
        'tags' => ['user:' . $this->currentUser->id()],
      ],
    ];

    return $content;
  }

  /**
   * Function for dynamic content rendering.
   * 
   * @param string $name
   *   Contains the arbitary name of the person.
   * @param string $company
   *   Contains the name of the company.
   * 
   * @return array
   *   Returns the render array.
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
