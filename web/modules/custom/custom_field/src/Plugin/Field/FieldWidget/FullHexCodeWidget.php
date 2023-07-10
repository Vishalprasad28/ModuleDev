<?php declare(strict_types = 1);

namespace Drupal\custom_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'full_hex_code' field widget.
 *
 * @FieldWidget(
 *   id = "full_hex_code",
 *   label = @Translation("Full Hex Code"),
 *   field_types = {"rgb_color_picker"},
 * )
 */
final class FullHexCodeWidget extends WidgetBase {

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
      '#element_validate' => array(
        array($this, 'minColorRangeValidate'),
      ),
      '#default_value' => $value,
    ];
    return $element;
  }

  /**
   * @method minColorRangeValidate()
   *   To Validated the color value whethere its within the range
   * 
   * @param array $element
   *   Receives the Field elements
   * @param FormStateInterface $form_state
   *   Receives the form_state
   * 
   * @return void
   */
  public function minColorRangeValidate($element, FormStateInterface $form_state) {
    $min_value = base_convert(substr($this->getSetting('rgb_min'), 1, 6), 16,10);
    $form_value = base_convert(substr($form_state->getValue('value'), 1, 6), 16,10);

    if ($form_value < $min_value) {
      $form_state->setErrorByName('value', $this->t('Value is outside the range'));
    }
  }
}
