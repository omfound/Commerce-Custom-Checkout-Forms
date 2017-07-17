<?php

namespace Drupal\commerce_custom_checkout_forms\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'checkout_form_wrapper_bundle_selector' field type.
 *
 * @FieldType(
 *   id = "checkout_form_wrapper_bundle_selector",
 *   label = @Translation("Checkout Form Wrapper Bundle Selector"),
 *   category =  @Translation("Commerce Custom Checkout Forms"),
 *   module = "commerce_custom_checkout_forms",
 *   default_widget = "checkout_form_wrapper_bundle_selector_select",
 *   default_formatter = "checkout_form_wrapper_bundle_selector_simple_text"
 * )
 */
class CheckoutFormWrapperBundleSelector extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'varchar',
          'length' => '255',
          'not null' => TRUE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(t('Bundle Value'));

    return $properties;
  }

}

