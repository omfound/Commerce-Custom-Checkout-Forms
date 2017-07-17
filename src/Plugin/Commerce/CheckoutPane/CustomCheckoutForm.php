<?php

namespace Drupal\commerce_custom_checkout_forms\Plugin\Commerce\CheckoutPane;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\FormState;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Provides the contact information pane.
 *
 * @CommerceCheckoutPane(
 *   id = "custom_checkout_form",
 *   label = @Translation("Custom Forms"),
 *   default_step = "order_information",
 *   wrapper_element = "fieldset",
 * )
 */
class CustomCheckoutForm extends CheckoutPaneBase implements CheckoutPaneInterface {
  /**
   * {@inheritdoc}
   */
  public function isVisible() {
    // Show the pane only for guest checkout.
    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneSummary() {
    /*return [
      '#plain_text' => $this->order->getEmail(),
    ];*/
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) { 
    if (!empty($pane_form['compound_pane_id'])) {
      $compound_pane_id = $pane_form['compound_pane_id']['#value'];
      $storage = $form_state->getStorage();
      $assets = $storage[$compound_pane_id];
      $checkout_form_wrapper = $this->entityTypeManager->getStorage('checkout_form_wrapper')->create(array('type' => $assets['bundle']));
      $form_object = $this->entityTypeManager->getFormObject('checkout_form_wrapper', 'default')->setEntity($checkout_form_wrapper);
      $checkout_form = $form_object->buildForm([], $form_state);
      $eb = array();
      foreach ($checkout_form['#entity_builders'] as $builder) {
        $eb[] = array($form_object, str_replace('::', '',  $builder));
      }
      $checkout_form['#entity_builders'] = $eb;
      unset($checkout_form['actions']);
      $pane_form[$compound_pane_id] = $checkout_form;
    }
    return $pane_form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function validatePaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    // Look for webform's submission handling.
    if (!empty($pane_form['compound_pane_id'])) {
      $compound_pane_id = $pane_form['compound_pane_id']['#value'];
      $triggering_element = &$form_state->getTriggeringElement();
      $triggering_element['#ief_submit_trigger'] = TRUE;
      $storage = $form_state->getStorage();
      $assets = $storage[$compound_pane_id];
      $checkout_form = $this->entityTypeManager->getStorage('checkout_form_wrapper')->create(array('type' => $assets['bundle']));
      $form_object = $this->entityTypeManager->getFormObject('checkout_form_wrapper', 'default')->setEntity($checkout_form);
      $form_object->submitForm($pane_form[$compound_pane_id], $form_state);
      $entity = $form_object->validateForm($pane_form[$compound_pane_id], $form_state);
      $form_object->setEntity($entity);
      $form_object->save($pane_form[$compound_pane_id], $form_state); 
    }
  }

}
