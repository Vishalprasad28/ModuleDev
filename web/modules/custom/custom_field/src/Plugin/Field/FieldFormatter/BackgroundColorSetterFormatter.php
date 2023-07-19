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
      $element[$delta] = [
        '#theme' => "dynamic-background-color",
        '#color' => $item->value,
      ];
    }
    return $element;
  }

}
