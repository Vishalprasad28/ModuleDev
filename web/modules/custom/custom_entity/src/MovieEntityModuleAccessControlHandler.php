<?php declare(strict_types = 1);

namespace Drupal\custom_entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the movie entity module entity type.
 *
 * phpcs:disable Drupal.Arrays.Array.LongLineDeclaration
 *
 * @see https://www.drupal.org/project/coder/issues/3185082
 */
final class MovieEntityModuleAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account): AccessResult {
    return match($operation) {
      'view' => AccessResult::allowedIfHasPermissions($account, ['view movie_entity', 'administer movie_entity'], 'OR'),
      'update' => AccessResult::allowedIfHasPermissions($account, ['edit movie_entity', 'administer movie_entity'], 'OR'),
      'delete' => AccessResult::allowedIfHasPermissions($account, ['delete movie_entity', 'administer movie_entity'], 'OR'),
      default => AccessResult::neutral(),
    };
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL): AccessResult {
    return AccessResult::allowedIfHasPermissions($account, ['create movie_entity', 'administer movie_entity'], 'OR');
  }

}
