<?php

/**
 * @file
 *   Contains the Custom Block Plugin for the hello world module
 */

namespace Drupal\helloworld\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a greeting block block.
 *
 * @Block(
 *   id = "helloworld_greeting_block",
 *   admin_label = @Translation("Greeting Block"),
 *   category = @Translation("Custom"),
 * )
 */
final class GreetingBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'user_detail' => 'role',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {

    $options = [
      'role' => $this->t('User Role'),
      'email' => $this->t('User email'),
      'uname' => $this->t('User Name'),
    ];

    $form['user_detail'] = [
      '#type' => 'radios',
      '#title' => $this->t('User Details'),
      '#options' => $options,
      '#description' => $this->t('Select the user role to be displayed'),
      '#default_value' => $this->configuration['user_detail'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['user_detail'] = $form_state->getValue('user_detail');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {

    $user = \Drupal::currentUser();
    $user_detail = $this->configuration['user_detail'];
    switch ($user_detail) {
      case 'role':
        $value = $user->getRoles()[0];
        break;
      case 'email':
        $value = $user->getEmail();
        break;
      case 'uname':
        $value = $user->getAccountName();
        break;
      default:
        $value = $user->getRoles()[0];
    }

    $build['content'] = [
      '#markup' => $this->t('This Block is coming from Hello World Module') . '<br>' . $this->t('Current user has @user ', ['@user' => $user_detail]) . '<strong>' . $value . '</strong>',
    ];
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account): AccessResult {
    // @todo Evaluate the access condition here.
    return AccessResult::allowedIf(TRUE);
  }

}
