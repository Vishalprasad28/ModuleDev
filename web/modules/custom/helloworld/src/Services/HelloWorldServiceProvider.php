<?php

/**
 * @file
 *   Contains the Service Alter Class
 */

 namespace Drupal\helloworld\Services;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

class HelloWorldServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    //Overriddig the helloworld.route_service service
    // Adding a new dependency to it

    if ($container->hasDefinition('helloworld.route_service')) {
      $details = $container->getDefinition('helloworld.route_service');
      $details->addArgument(new Reference('config.factory'));
    }
  }
}