<?php

/**
 * @file
 *   Contains the BasicRouting class
 */

namespace Drupal\helloworld\Routing;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Url;
use Symfony\Component\Routing\RouteCollection;

class BasicRouting extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $route = $collection->get('helloworld.template_route');
    $route->setRequirement('_role', 'administrator');
  }

  /**
   * @method redirectAfterSubmit()
   *   This methode defines a custom redirect for a form
   * 
   * @param string $route_name
   *   Takes the route name to redirect to
   * @param FormStateInterface $form_state
   *   Takes the form_state of the form
   * @param array &$form
   *   Takes the prebuilt form element array
   * 
   * @return void
   */
  public function redirectAfterSubmit(string $route_name, &$form, FormStateInterface $form_state) {
    $url = Url::fromRoute('helloworld.custom_welcome_page');
    $form_state->setRedirectUrl($url);
  }
}
