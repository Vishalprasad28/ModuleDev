<?php declare(strict_types = 1);

namespace Drupal\custom_field\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the 'field_rgb_component' field widget.
 *
 * @FieldWidget(
 *   id = "field_rgb_component",
 *   label = @Translation("RGB Component"),
 *   field_types = {"rgb_color_picker"},
 * )
 */
final class FieldRgbComponentWidget extends WidgetBase {

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
    
    $element['red'] =  [
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
      '#element_validate' => array(
        array($this, 'minColorRangeValidate'),
      ),
      '#pattern' => '^[a-fA-F0-9]{2}$',
      '#default_value' => $blue,
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
  public function minColorRangeValidate(array $element, FormStateInterface $form_state) {

    $color_value = $form_state->getValue('red') . $form_state->getValue('green') . $form_state->getValue('blue');
    $min_value = base_convert(substr($this->getSetting('rgb_min'), 1, 6), 16, 10);
    $form_value = base_convert($color_value, 16, 10);

    if ($form_value < $min_value) {
      $form_state->setErrorByName('red', $this->t('Value is outside the range'));
      $form_state->setErrorByName('green', $this->t('Value is outside the range'));
      $form_state->setErrorByName('blue', $this->t('Value is outside the range'));
    }
  }
}
