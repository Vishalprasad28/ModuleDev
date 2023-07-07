<?php declare(strict_types = 1);

namespace Drupal\custom_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'rgb_picker_widget' field widget.
 *
 * @FieldWidget(
 *   id = "rgb_picker_widget",
 *   label = @Translation("RGB Picker Widget"),
 *   field_types = {"rgb_color_picker"},
 * )
 */
final class RgbPickerWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {
    $element['value'] = $element + [
      '#type' => 'color',
      '#title' => $this->t('Pick the color'),
      '#default_value' => $items[$delta]->value ?? NULL,
    ];
    return $element;
  }

}
