<?php

namespace Drupal\custom_entity\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Movie Entity module budget settings for this site.
 */
final class BudgetForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'custom_entity_budget';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['custom_entity.budget'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['budget_amount'] = [
      '#type' => 'number',
      '#title' => $this->t('Enter the Movie Budget'),
      '#description' => $this->t('Enter the amount that lies in range 0 and 90000'),
      '#default_value' => $this->config('custom_entity.budget')->get('budget'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if ($form_state->getValue('budget_amount') <= 0 || $form_state->getValue('budget_amount') > 90000) {
      $form_state->setErrorByName('budget_amount', $this->t('Please enter a valid amount that lies within the specified range'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('custom_entity.budget')
      ->set('budget', $form_state->getValue('budget_amount'))
      ->save();
    parent::submitForm($form, $form_state);
    $form_state->setRedirect('<front>');
  }

}
