<?php

namespace Drupal\node_uninstaller;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Service class to handle the Node Bundle deletion process.
 */
class NodeUninstallationHandler {

  /**
   * Contains the Entity Type Manager Object.
   * 
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  public $entityManager;

  /**
   * Constructs the Entity Type Manager Object to manage drupal Entities.
   * 
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   Contains the EntityTypeManagerInterface Object.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * Function to ask for user permission to delete the nodes and entity.
   *   
   *   before user uninstalls the module that creates its custom entity
   *   bundle it will ask for whether the user wants to delete all the nodes
   *   being created.
   * 
   * @param string $entity_bundle_name
   *   Contains the Bundle name of the entity.
   */
  public function uninstallConfirmation(string $entity_bundle_name) {
    // Loading the content type.
    $content_type = $this->entityManager->getStorage('node_type')->load($entity_bundle_name);
    
    // If the Content type exists, setting its status to FALSE.
    if ($content_type) {
      $content_type->set('status', FALSE);
      $content_type->save();
    }

    // Delete all nodes of given content type.
    $storage_handler = $this->entityManager->getStorage('node');
    $nodes = $storage_handler->loadByProperties(['type' => $entity_bundle_name]);

    if ($nodes) {
      ask_again:
      echo 'There are nodes created of' . ' ' . $entity_bundle_name . ' ' . 'type, do you wanna delete them all?(y/n):';
      $input = strtolower(trim(fgets(STDIN)));
      if ($input === 'n') {
        throw new \Exception('Uninstallation terminated, Please delete all nodes of' . ' ' . $entity_bundle_name . ' ' .  'first');
      }
      else if ($input === 'y') {
        echo 'Deleting all the nodes of ' . $entity_bundle_name . '...';
        $storage_handler->delete($nodes);
      }
      else {
        goto ask_again;
      }
    }

    // Deleting content type.
    if ($content_type) {
      $content_type->delete();
    }
  }

}
