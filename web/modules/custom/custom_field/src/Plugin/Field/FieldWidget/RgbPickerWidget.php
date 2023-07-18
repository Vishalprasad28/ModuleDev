<?php

namespace Drupal\custom_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Defines the 'rgb_picker_widget' field widget.
 *
 * @FieldWidget(
 *   id = "rgb_picker_widget",
 *   label = @Translation("RGB Picker Widget"),
 *   field_types = {"rgb_color_picker"},
 * )
 */
final class RgbPickerWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    if ($items[$delta]->value) {
      $value = $items[$delta]->value;
    }
    elseif ($items[$delta]->red && $items[$delta]->green && $items[$delta]->blue) {
      $value = '#' . $items[$delta]->red . $items[$delta]->green . $items[$delta]->blue;
    }
    else {
      $value = '';
    }
    $element['value'] = $element + [
      '#type' => 'color',
      '#title' => $this->t('Pick the color'),
      '#default_value' => $value,
    ];
    return $element;
  }

}
