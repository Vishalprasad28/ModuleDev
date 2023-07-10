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
    $element['red'] =  [
      '#type' => 'textfield',
      '#title' => $this->t('Red'),
      '#default_value' => $items[$delta]->red ?? '',
    ];
    $element['green'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Green'),
      '#default_value' => $items[$delta]->green ?? '',
    ];
    $element['blue'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Blue'),
      '#default_value' => $items[$delta]->blue ?? '',
    ];

    return $element;
  }
}
