<?php

namespace Drupal\custom_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'Background Color Setter' formatter.
 *
 * @FieldFormatter(
 *   id = "background_color_setter",
 *   label = @Translation("Background Color Setter"),
 *   field_types = {"rgb_color_picker"},
 * )
 */
final class BackgroundColorSetterFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      if ($item->value) {
        $value = $item->value;
      }
      else {
        $value = '#' . $item->red . $item->green . $item->blue;
      }
      $element[$delta] = [
        '#theme' => "dynamic-background-color",
        '#color' => $value,
      ];
    }
    return $element;
  }

}
