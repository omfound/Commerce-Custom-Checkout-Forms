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
 *   default_step = "required_forms",
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
    $storage = $form_state->getStorage();
    if (!empty($storage['required_form_storage'])) {
      $pane_form['#title'] = t('Required Forms for @entity', array('@entity' =>  $storage['required_form_storage']['product']->getTitle()));
      if (empty($storage['required_form_storage']['checkout_form_wrapper'])) {
        $checkout_form_wrapper = $this->entityTypeManager->getStorage('checkout_form_wrapper')->create(['type' => $storage['required_form_storage']['bundle']]);
      }
      else {
        $checkout_form_wrapper = $storage['required_form_storage']['checkout_form_wrapper'];
      }
      $form_object = $this->entityTypeManager->getFormObject('checkout_form_wrapper', 'default')->setEntity($checkout_form_wrapper);
      $checkout_form = $form_object->buildForm([], $form_state);
      $eb = array();
      foreach ($checkout_form['#entity_builders'] as $builder) {
        $eb[] = array($form_object, str_replace('::', '',  $builder));
      }
      $checkout_form['#entity_builders'] = $eb;
      if (!empty($checkout_form['checkout_forms'])) {
        // Storage has been updated since first call.
        $storage = $form_state->getStorage();
        $ief_instance = array_shift($storage['inline_entity_form']);
        $current_count = count($ief_instance['entities']);
        $required_count = (int) $storage['required_form_storage']['order_item']->getQuantity();
        $string_values = array(
          '@entity' =>  $storage['required_form_storage']['product']->getTitle(),
          '@start' => $current_count,
          '@end' => $required_count
        );
        $checkout_form['checkout_forms']['widget']['description'] = array(
          '#type' => 'html_tag',
          '#tag' => 'p',
          '#value' => t('@entity requires that you fill out a form for each item you are purchasing. @start of @end forms submitted.', $string_values),
          '#weight' => -100
        );
        if ($current_count == $required_count) {
          unset($checkout_form['checkout_forms']['widget']['actions']);
        } 
      }
      unset($checkout_form['actions']);
      $pane_form['custom_checkout_form'] = $checkout_form;
    }
    return $pane_form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function validatePaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    if (!empty($pane_form['custom_checkout_form']['checkout_forms'])) {
      $storage = $form_state->getStorage();
      if (!empty($storage['required_form_storage']['order_item'])) {
        $quantity = $storage['required_form_storage']['order_item']->getQuantity();
        $checkout_forms = $form_state->getValue('checkout_forms');
        if (count($checkout_forms['entities']) != $quantity) {
          $form_state->setErrorByName('custom_checkout_form', t('You must fill out a item form for each item you are purchasing.'));
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    // Look for webform's submission handling.
    $storage = $form_state->getStorage();
    if (!empty($storage['required_form_storage'])) {
      $triggering_element = &$form_state->getTriggeringElement();
      $triggering_element['#ief_submit_trigger'] = TRUE;
      $storage = $form_state->getStorage();
      if (empty($storage['required_form_storage']['checkout_form_wrapper'])) {
        $checkout_form_wrapper = $this->entityTypeManager->getStorage('checkout_form_wrapper')->create(['type' => $storage['required_form_storage']['bundle']]);
      }
      else {
        $checkout_form_wrapper = $storage['required_form_storage']['checkout_form_wrapper'];
      }
      $form_object = $this->entityTypeManager->getFormObject('checkout_form_wrapper', 'default')->setEntity($checkout_form_wrapper);
      $form_object->submitForm($pane_form['custom_checkout_form'], $form_state);
      $entity = $form_object->validateForm($pane_form['custom_checkout_form'], $form_state);
      $entity->set('name', $storage['required_form_storage']['title']);
      $entity->set('order_id', $this->order->id());
      $entity->set('product_id', $storage['required_form_storage']['product']->id());
      $entity->set('product_variation_id', $storage['required_form_storage']['product_variation']->id());
      if ($entity->hasField('checkout_forms')) {
        $child_forms_field = $entity->get('checkout_forms');
        if (!$child_forms_field->isEmpty()) {
          $child_forms = $child_forms_field->getValue();
          foreach($child_forms AS $key => $value) {
            if (!empty($value['entity'])) {
              $value['entity']->setName($storage['required_form_storage']['title'] . ' ' . $key);
            }
          }
        }
      }
      $form_object->setEntity($entity);
      $form_object->save($pane_form['custom_checkout_form'], $form_state); 
    }
  }
}
