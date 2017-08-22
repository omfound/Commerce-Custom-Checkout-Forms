<?php

namespace Drupal\commerce_custom_checkout_forms\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Checkout form wrapper type entity.
 *
 * @ConfigEntityType(
 *   id = "checkout_form_wrapper_type",
 *   label = @Translation("Checkout form wrapper type"),
 *   handlers = {
 *     "list_builder" = "Drupal\commerce_custom_checkout_forms\CheckoutFormWrapperTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormWrapperTypeForm",
 *       "edit" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormWrapperTypeForm",
 *       "delete" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormWrapperTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_custom_checkout_forms\CheckoutFormWrapperTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "checkout_form_wrapper_type",
 *   admin_permission = "administer checkout form wrapper types",
 *   bundle_of = "checkout_form_wrapper",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/checkout_form_wrapper_type/{checkout_form_wrapper_type}",
 *     "add-form" = "/admin/commerce/checkout_form_wrapper_type/add",
 *     "edit-form" = "/admin/commerce/checkout_form_wrapper_type/{checkout_form_wrapper_type}/edit",
 *     "delete-form" = "/admin/commerce/checkout_form_wrapper_type/{checkout_form_wrapper_type}/delete",
 *     "collection" = "/admin/commerce/checkout_form_wrapper_type"
 *   }
 * )
 */
class CheckoutFormWrapperType extends ConfigEntityBundleBase implements CheckoutFormWrapperTypeInterface {

  /**
   * The Checkout form wrapper type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Checkout form wrapper type label.
   *
   * @var string
   */
  protected $label;

}
