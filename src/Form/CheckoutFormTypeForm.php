<?php

namespace Drupal\commerce_custom_checkout_forms\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CheckoutFormTypeForm.
 *
 * @package Drupal\commerce_custom_checkout_forms\Form
 */
class CheckoutFormTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $checkout_form_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $checkout_form_type->label(),
      '#description' => $this->t("Label for the Checkout form type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $checkout_form_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormType::load',
      ],
      '#disabled' => !$checkout_form_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $checkout_form_type = $this->entity;
    $status = $checkout_form_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Checkout form type.', [
          '%label' => $checkout_form_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Checkout form type.', [
          '%label' => $checkout_form_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($checkout_form_type->toUrl('collection'));
  }

}
