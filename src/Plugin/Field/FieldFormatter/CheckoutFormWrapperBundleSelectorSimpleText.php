<?php

namespace Drupal\commerce_custom_checkout_forms\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'checkout_form_wrapper_bundle_selector_simple_text' formatter.
 *
 * @FieldFormatter(
 *   id = "checkout_form_wrapper_bundle_selector_simple_text",
 *   module = "commerce_custom_checkout_forms",
 *   label = @Translation("Simple text-based formatter"),
 *   field_types = {
 *     "checkout_form_wrapper_bundle_selector"
 *   }
 * )
 */
class CheckoutFormWrapperBundleSelectorSimpleText extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();

    foreach ($items as $delta => $item) {
      $elements[$delta] = array(
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $this->t('Bundle: @bundle', array('@bundle' => $item->value)),
      );
    }

    return $elements;
  }

}
