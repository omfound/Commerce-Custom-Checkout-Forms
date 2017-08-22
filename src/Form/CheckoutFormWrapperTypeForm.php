<?php

namespace Drupal\commerce_custom_checkout_forms\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Extension\ModuleHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CheckoutFormWrapperTypeForm.
 *
 * @package Drupal\commerce_custom_checkout_forms\Form
 */
class CheckoutFormWrapperTypeForm extends EntityForm {

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler')
    );
  }

  public function __construct($module_handler) {
    $this->module_handler = $module_handler;
  }
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $checkout_form_wrapper_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $checkout_form_wrapper_type->label(),
      '#description' => $this->t("Label for the Checkout form wrapper type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $checkout_form_wrapper_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormWrapperType::load',
      ],
      '#disabled' => !$checkout_form_wrapper_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $checkout_form_wrapper_type = $this->entity;
    $status = $checkout_form_wrapper_type->save();

    switch ($status) {
      case SAVED_NEW:
        commerce_custom_checkout_forms_add_checkout_form_reference_field($checkout_form_wrapper_type);
        // @NB We allow other modules to attach fields here.
        // The hooks for doing so are in flux between 8.3.x and 8.5.x.
        // This seems like the best solution for now.
        $this->module_handler->invokeAll('checkout_form_wrapper_entity_field_info', array('type' => $checkout_form_wrapper_type));
        drupal_set_message($this->t('Created the %label Checkout form wrapper type.', [
          '%label' => $checkout_form_wrapper_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Checkout form wrapper type.', [
          '%label' => $checkout_form_wrapper_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($checkout_form_wrapper_type->toUrl('collection'));
  }

}
