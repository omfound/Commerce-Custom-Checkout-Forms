<?php

namespace Drupal\commerce_custom_checkout_forms\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'checkout_form_wrapper_bundle_selector_select' widget.
 *
 * @FieldWidget(
 *   id = "checkout_form_wrapper_bundle_selector_select",
 *   module = "commerce_custom_checkout_forms",
 *   label = @Translation("Bundle Selector"),
 *   field_types = {
 *     "checkout_form_wrapper_bundle_selector"
 *   }
 * )
 */
class CheckoutFormWrapperBundleSelectorSelect extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $bundles = \Drupal::entityManager()->getBundleInfo('checkout_form_wrapper');
    $bundle_options = array('' => 'None');
    foreach($bundles as $key => $bundle) {
      $bundle_options[$key] = $bundle['label'];
    }
    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $element += array(
      '#type' => 'select',
      '#default_value' => $value,
      '#options' => $bundle_options,
    );
    return array('value' => $element);
  }

}

