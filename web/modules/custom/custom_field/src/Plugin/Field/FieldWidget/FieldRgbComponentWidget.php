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
    if ($items[$delta]->value) {
      $red = substr($items[$delta]->value, 1, 2);
      $green = substr($items[$delta]->value, 3, 4);
      $blue = substr($items[$delta]->value, 5, 6);
    }
    elseif ($items[$delta]->red && $items[$delta]->green && $items[$delta]->blue) {
      $red = $items[$delta]->red;
      $green = $items[$delta]->green;
      $blue = $items[$delta]->blue;
    }
    else {
      $red = '';
      $green = '';
      $blue = '';
    }

    $element['red'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Red'),
      '#description' => $this->t('Write in formate ff'),
      '#maxlength' => 2,
      '#pattern' => '^[a-fA-F0-9]{2}$',
      '#default_value' => $red,
    ];
    $element['green'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Green'),
      '#description' => $this->t('Write in formate ff'),
      '#maxlength' => 2,
      '#pattern' => '^[a-fA-F0-9]{2}$',
      '#default_value' => $green,
    ];
    $element['blue'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Blue'),
      '#description' => $this->t('Write in formate ff'),
      '#maxlength' => 2,
      '#pattern' => '^[a-fA-F0-9]{2}$',
      '#default_value' => $blue,
    ];

    return $element;
  }

}
