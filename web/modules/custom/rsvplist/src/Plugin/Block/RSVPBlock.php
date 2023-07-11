<?php

namespace Drupal\rsvplist\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides The RSVP main block.
 *
 * @Block(
 *   id = "rsvp_block",
 *   admin_label = @Translation("RSVP Block")
 * )
 */
class RSVPBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Fetching the RSVP form we just made.
    $RSVP_form = \Drupal::formBuilder()->getForm('Drupal\rsvplist\Form\RSVPForm');
    return $RSVP_form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockAccess(AccountInterface $account) {
    // If Viewing the node, get the full node object.
    $node = \Drupal::routeMatch()->getParameter('node');

    if (!(is_null($node))) {
      // Checking if the account has permission.
      $has_permission = AccessResult::allowedIfHasPermission($account, 'View RSVP List
      ');
      return $has_permission;
    }

    return AccessResult::forbidden();
  }

}
