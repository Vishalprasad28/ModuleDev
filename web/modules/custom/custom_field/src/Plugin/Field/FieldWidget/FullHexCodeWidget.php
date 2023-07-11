<?php

declare(strict_types = 1);

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

}
