<?php

namespace Drupal\custom_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Defines the 'full_hex_code' field widget.
 *
 * @FieldWidget(
 *   id = "full_hex_code",
 *   label = @Translation("Full Hex Code"),
 *   field_types = {"rgb_color_picker"},
 * )
 */
final class FullHexCodeWidget extends WidgetBase implements ContainerFactoryPluginInterface {

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
      '#type' => 'textfield',
      '#title' => $this->t('RGB Color Code'),
      '#pattern' => '^[#][a-fA-F0-9]{6}$',
      '#maxlength' => 7,
      '#default_value' => $value,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $index => $value) {
      // Discarding the #000000 value that comes as default for the picker.
      // Removing it because while adding the unlimited fields in the contents,
      // the default #000000 value also gets stored, everytime the content is 
      // edited.
      if ($value['value'] == '#000000') {
        unset($values[$index]);
        continue;
      }
    }
    return $values;
  }

}
