<?php

namespace Drupal\commerce_custom_checkout_forms\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Checkout form type entity.
 *
 * @ConfigEntityType(
 *   id = "checkout_form_type",
 *   label = @Translation("Checkout form type"),
 *   handlers = {
 *     "list_builder" = "Drupal\commerce_custom_checkout_forms\CheckoutFormTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormTypeForm",
 *       "edit" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormTypeForm",
 *       "delete" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormTypeDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_custom_checkout_forms\CheckoutFormTypeHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "checkout_form_type",
 *   admin_permission = "administer checkout form types",
 *   bundle_of = "checkout_form",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/checkout_form_type/{checkout_form_type}",
 *     "add-form" = "/admin/commerce/checkout_form_type/add",
 *     "edit-form" = "/admin/commerce/checkout_form_type/{checkout_form_type}/edit",
 *     "delete-form" = "/admin/commerce/checkout_form_type/{checkout_form_type}/delete",
 *     "collection" = "/admin/commerce/checkout_form_type"
 *   }
 * )
 */
class CheckoutFormType extends ConfigEntityBundleBase implements CheckoutFormTypeInterface {

  /**
   * The Checkout form type ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Checkout form type label.
   *
   * @var string
   */
  protected $label;

}
