<?php

/**
 * @file
 * Contains the hook definitions for the module helloworld.
 */

/**
 * Hook_hello_greet() definition.
 *
 *   This Hook prints the number of times a current node has been visited in
 *   the current active session.
 *
 * We need to pass the count value to it from the other hooks being implemented.
 *
 * @param int $count
 *   Accepts the count of number of times nodes have been visited.
 * @param string $bundle_name
 *   Accepts the bundle name of the entity.
 * @param array &$build
 *   Accepts the build render array of the entity.
 *
 * @return void
 *   Returns Void.
 */
function hook_hello_greet(int $count, string $bundle_name, array &$build) {
  // Displayes the Number of tiems the node has been visited.
  // Adding some additional information to the markup of all entities.
  // Information contains the View Count of the Node.
  $build['hello_world'] = [
    '#type' => 'markup',
    '#markup' => t('This Node is of @bundle_type and it has been visited @count times',
      ['@bundle_type' => $bundle_name, '@count' => $count]),
  ];
}
