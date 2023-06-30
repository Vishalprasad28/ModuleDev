<?php

/**
 * @file
 *   Contains the BasicRouting class
 */

namespace Drupal\helloworld\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class BasicRouting extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $route = $collection->get('helloworld.template_route');
    $route->setRequirement('_role', 'administrator');
  }
}