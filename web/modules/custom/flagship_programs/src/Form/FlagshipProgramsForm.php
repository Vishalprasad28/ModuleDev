<?php

namespace Drupal\flagship_programs\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * A Config Form to take the Flagship programs data as input.
 */
class FlagshipProgramsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'flagship_programs_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
      'flagship_programs.data',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['flagship_programs']['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('City'),
    ];
    $form['flagship_programs']['theater'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Theater'),
    ];
    $form['flagship_programs']['time'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Time'),
      '#date_date_element' => 'none',
      '#date_time_element' => 'time',
      '#date_time_format' => 'H:i',
      '#default_value' => '00:00',
    ];
    $form['flagship_programs']['template'] = [
      '#type' => 'select',
      '#title' => $this->t('Select template'),
      '#options' => [
        'table' => $this->t('Table'),
        'list' => $this->t('List'),
      ],
      '#default_value' => $this->config('flagship_programs.data')->get('template'),
    ];

    $form['flagship_programs']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $data = $this->config('flagship_programs.data')->get('data') ?
      $this->config('flagship_programs.data')->get('data') :
      [];
    $temp = [
      'city' => $form_state->getValue('city'),
      'theater' => $form_state->getValue('theater'),
      'time' => $form_state->getValue('time')->format('H-i-s'),
    ];
    array_push($data, $temp);
    $this->config('flagship_programs.data')
      ->set('data', $data)
      ->set('template', $form_state->getValue('template'))
      ->save();
  }

}
