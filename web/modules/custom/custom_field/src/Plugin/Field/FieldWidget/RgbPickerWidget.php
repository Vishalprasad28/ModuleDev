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
    $element['value'] = $element + [
      '#type' => 'color',
      '#title' => $this->t('Pick the color'),
      '#default_value' => $items[$delta]->value ? $items[$delta]->value : '',
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
