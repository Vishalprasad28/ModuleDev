<?php

declare(strict_types = 1);

namespace Drupal\custom_field\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the Hex Code Formatter constraint.
 */
final class HexCodeFormatterConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate(mixed $value, Constraint $constraint): void {

    // Adding the Validation Criteria.
    if (!is_null($value) && !preg_match('/^[#][a-f0-9]{6}$/', strtolower($value))) {
      $this->context->addViolation($constraint->hex_format, ['@value' => $value]);
    }
  }

}
