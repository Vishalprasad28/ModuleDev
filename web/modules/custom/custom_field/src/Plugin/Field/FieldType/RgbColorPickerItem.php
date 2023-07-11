<?php

namespace Drupal\custom_field\Plugin\Field\FieldType;

use Drupal\Component\Utility\Random;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Defines the 'rgb_color_picker' field type.
 *
 * @FieldType(
 *   id = "rgb_color_picker",
 *   label = @Translation("RGB Color Picker"),
 *   category = @Translation("General"),
 *   default_widget = "full_hex_code",
 *   default_formatter = "static_hex_code",
 * )
 */
final class RgbColorPickerItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings(): array {
    $settings = ['rgb' => '#ffffff'];
    return $settings + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data): array {
    $element['rgb'] = [
      '#type' => 'textfield',
      '#title' => $this->t('RGB Color Code'),
      '#pattern' => '^[#][a-fA-F0-9]{6}$',
      '#maxlength' => 7,
      '#default_value' => $this->getSetting('rgb'),
      '#disabled' => $has_data,
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings(): array {
    $settings = [
      'rgb_min' => '#000000',
    ];
    return $settings + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state): array {
    $element['rgb_min'] = [
      '#type' => 'textfield',
      '#title' => $this->t('RGB Color Min value'),
      '#maxlength' => 7,
      '#pattern' => '^[#][a-fA-F0-9]{6}$',
      '#default_value' => $this->getSetting('rgb_min'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty(): bool {
    $is_empty = $this->get('value')->getValue() == NULL && ($this->get('red')->getValue() == NULL && $this->get('green')->getValue() == NULL && $this->get('blue')->getValue() == NULL);
    return $is_empty;
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition): array {

    // See /core/lib/Drupal/Core/TypedData/Plugin/DataType directory for
    // available data types.
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(('Text value'))
      ->addConstraint('HexCodeFormatter');

    // Properties for the red component.
    $properties['red'] = DataDefinition::create('string')->setLabel('red');

    // Properties for the green component.
    $properties['green'] = DataDefinition::create('string')->setLabel('green');

    // Properties for the blue component.
    $properties['blue'] = DataDefinition::create('string')->setLabel('blue');

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints(): array {
    $constraints = parent::getConstraints();

    $constraint_manager = $this->getTypedDataManager()->getValidationConstraintManager();

    // @DCG Suppose our value must not be longer than 6 characters.
    $options['value']['Length']['max'] = 7;
    $options['red']['Length']['max'] = 2;
    $options['green']['Length']['max'] = 2;
    $options['blue']['Length']['max'] = 2;

    // See /core/lib/Drupal/Core/Validation/Plugin/Validation/Constraint
    // directory for available constraints.
    $constraints[] = $constraint_manager->create('ComplexData', $options);
    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition): array {

    $columns = [
      'value' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'RGB Hex code for a color',
        'length' => 7,
      ],
      'red' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'RGB Hex code for a color',
        'length' => 2,
      ],
      'green' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'RGB Hex code for a color',
        'length' => 2,
      ],
      'blue' => [
        'type' => 'varchar',
        'not null' => FALSE,
        'description' => 'RGB Hex code for a color',
        'length' => 2,
      ],
    ];

    $schema = [
      'columns' => $columns,
    ];

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition): array {
    $random = new Random();
    $values['value'] = $random->word(mt_rand(6, 6));
    return $values;
  }

}
