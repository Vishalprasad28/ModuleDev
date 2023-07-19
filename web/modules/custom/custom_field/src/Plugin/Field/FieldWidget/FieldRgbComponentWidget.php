<?php

namespace Drupal\custom_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Defines the 'field_rgb_component' field widget.
 *
 * @FieldWidget(
 *   id = "field_rgb_component",
 *   label = @Translation("RGB Component"),
 *   field_types = {"rgb_color_picker"},
 * )
 */
final class FieldRgbComponentWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element['red'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Red'),
      '#description' => $this->t('Write in formate ff'),
      '#maxlength' => 2,
      '#pattern' => '^[a-fA-F0-9]{2}$',
      '#default_value' => isset($items[$delta]->value) ? substr($items[$delta]->value, 1, 2) : '',
    ];
    $element['green'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Green'),
      '#description' => $this->t('Write in formate ff'),
      '#maxlength' => 2,
      '#pattern' => '^[a-fA-F0-9]{2}$',
      '#default_value' => isset($items[$delta]->value) ? substr($items[$delta]->value, 3, 2) : '',
    ];
    $element['blue'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Blue'),
      '#description' => $this->t('Write in formate ff'),
      '#maxlength' => 2,
      '#pattern' => '^[a-fA-F0-9]{2}$',
      '#default_value' => isset($items[$delta]->value) ? substr($items[$delta]->value, 5, 2) : '',
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {

    foreach ($values as $index => $value) {
      $full_rgb = '#' . $value['red'] . $value['green'] . $value['blue'];
      $values[$index]['value'] = $full_rgb;
      unset($values[$index]['red']);
      unset($values[$index]['green']);
      unset($values[$index]['blue']);
    }
    return $values;
  }

}
