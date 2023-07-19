<?php

declare(strict_types = 1);

namespace Drupal\custom_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'Static Hex Code' formatter.
 *
 * @FieldFormatter(
 *   id = "static_hex_code",
 *   label = @Translation("Static Hex Code"),
 *   field_types = {"rgb_color_picker"},
 * )
 */
final class StaticHexCodeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $element = [];
    foreach ($items as $delta => $item) {
      $element[$delta] = [
        '#markup' => $item->value,
      ];
    }
    return $element;
  }

}
