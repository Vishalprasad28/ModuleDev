<?php

declare(strict_types = 1);

namespace Drupal\custom_field\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * Provides a Hex Code Formatter constraint.
 *
 * @Constraint(
 *   id = "HexCodeFormatter",
 *   label = @Translation("Hex Code Formatter", context = "Validation"),
 * )
 */
final class HexCodeFormatterConstraint extends Constraint {

  /**
   * This message is shown when value is not in proper hex formate.
   * 
   * @var string
   */
  public $hex_format = '@value is not in proper hex format';

  /**
   * This message is show when the value is outside the defined range.
   * 
   * @var string
   */
  public $range_error = '@value is out of the range';

}
