<?php

/**
 * @file
 * Contains the Controller code for the simple page
 */
namespace Drupal\helloworld\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\user\Entity\User;

class SimpleController extends ControllerBase {

  /**
   * @var object
   */
  private $current_user;

  public function __construct() {
    $this->current_user = \Drupal::currentUser();
  }

  /**
   * @method staticPage()
   * 
   * @return array
   */
  public function staticPage() {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('This is just a static page'),
    ];
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

  /**
   * @method newController()
   * 
   * @return array
   */
  public function newController() {
    $var1 = 'Hello Controller data';
    $var2 = 'Arti';
    $var3 = ['Arko', 'Deepok', 'pratyusha'];

    return [
      '#theme' => 'helloworld_theme_hook',
      '#var1' => $var1,
      '#var2' => $var2,
      '#var3' => $var3,
    ];
  }

  /**
   * @method dynamicTite()
   * 
   * @return string
   */
  public function dynamicTitle() {
    //Geting the current logged in user
    $user = User::load(\Drupal::currentUser()->id());

    $account_name = $user->getAccountName();
    return 'Page title being served for' . ' ' . $account_name ;
  }

  /**
   * @method checkAccess()
   * 
   * @return \Drupal\Core\Access\AccessResultInterface
   *   Returns the permission to the route access
   */
  public function checkAccess() {
    return AccessResult::allowedIf($this->current_user->hasPermission('access the custom page'));
  }

  /**
   * @method campaignValueFetch()
   * 
   * @param int $val
   *   Takes an integer parameter from the url
   * @return array
   *   returns the render array to display on the page.
   */
  public function campaignValueFetch(int $val) {
    return[
      '#type' => 'markup',
      '#markup' => t('Campaign Number of the Campaign is: @val', ['@val' => $val]),
    ];
  }
}
