<?php

namespace Drupal\commerce_custom_checkout_forms\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowWithPanesBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the default multistep checkout flow.
 *
 * @CommerceCheckoutFlow(
 *   id = "custom_checkout_form_default",
 *   label = "Custom Checkout Form Default",
 * )
 */
class CustomCheckoutFormDefault extends CheckoutFlowWithPanesBase {

  /**
   * {@inheritdoc}
   */
  public function getSteps() {
    // Note that previous_label and next_label are not the labels
    // shown on the step itself. Instead, they are the labels shown
    // when going back to the step, or proceeding to the step.
    return [
      'login' => [
        'label' => $this->t('Login'),
        'previous_label' => $this->t('Go back'),
        'has_sidebar' => FALSE,
      ],
      'required_forms' => [
        'label' => $this->t('Required forms'),
        'has_sidebar' => TRUE,
        'previous_label' => $this->t('Go Back'),
        'next_label' => $this->t('Continue'),
      ],
      'order_information' => [
        'label' => $this->t('Order information'),
        'has_sidebar' => TRUE,
        'previous_label' => $this->t('Go back'),
        'next_label' => $this->t('Continue'),
      ],
      'review' => [
        'label' => $this->t('Review'),
        'next_label' => $this->t('Continue to review'),
        'previous_label' => $this->t('Go back'),
        'has_sidebar' => TRUE,
      ],
    ] + parent::getSteps();
  }
  public function buildForm(array $form, FormStateInterface $form_state, $step_id = NULL) {
    //$form = parent::buildForm($form, $form_state, $step_id);
    if (empty($step_id)) {
      throw new \InvalidArgumentException('The $step_id cannot be empty.');
    }
    $steps = $this->getVisibleSteps();
    $form['#tree'] = TRUE;
    $form['#step_id'] = $step_id;
    $form['#title'] = $steps[$step_id]['label'];
    $form['#theme'] = ['commerce_checkout_form'];
    $form['#attached']['library'][] = 'commerce_checkout/form';
    if ($this->hasSidebar($step_id)) {
      $form['sidebar']['order_summary'] = [
        '#type' => 'view',
        '#name' => 'commerce_checkout_order_summary',
        '#display_id' => 'default',
        '#arguments' => [$this->order->id()],
        '#embed' => TRUE,
      ];
    }
    $form['actions'] = $this->actions($form, $form_state);
    foreach ($this->getVisiblePanes($step_id) as $pane_id => $pane) {
      if ($pane_id == 'custom_checkout_form') {
        unset($form[$pane_id]);
        $items = $this->order->getItems();
        foreach ($items AS $item) {
          $product_variation = $item->getPurchasedEntity();
          $product = $product_variation->getProduct();       
          $wrapper_field = $product->get('field_checkout_form_wrapper');
          if (!$wrapper_field->isEmpty()) {
            $bundle = $wrapper_field->getValue()[0]['value'];
            $compound_pane_id = $pane_id . '_' . $product_variation->id();
            $form[$compound_pane_id] = [
              '#parents' => [$compound_pane_id],
              '#type' => $pane->getWrapperElement(),
              '#title' => $product->getTitle()
            ];
            $form[$compound_pane_id]['compound_pane_id'] = [
              '#type' => 'value',
              '#value' => $compound_pane_id
            ];
            $storage = $form_state->getStorage();
            $storage[$compound_pane_id] = [
              'bundle' => $bundle,
              'product_variation' => $product_variation,
              'product' => $product,
              'order_item' => $item
            ];
            $form_state->setStorage($storage);
            $form[$compound_pane_id] = $pane->buildPaneForm($form[$compound_pane_id], $form_state, $form);
          }
        }
      }
      else {
        $form[$pane_id] = [
          '#parents' => [$pane_id],
          '#type' => $pane->getWrapperElement(),
          '#title' => $pane->getLabel(),
        ];
        $form[$pane_id] = $pane->buildPaneForm($form[$pane_id], $form_state, $form);
      }
    }
    if ($this->hasSidebar($step_id)) {
      // The base class adds a hardcoded order summary view to the sidebar.
      // Remove it, there's a pane for that.
      unset($form['sidebar']);

      foreach ($this->getVisiblePanes('_sidebar') as $pane_id => $pane) {
        $form['sidebar'][$pane_id] = [
          '#parents' => ['sidebar', $pane_id],
          '#type' => $pane->getWrapperElement(),
          '#title' => $pane->getLabel(),
        ];
        $form['sidebar'][$pane_id] = $pane->buildPaneForm($form['sidebar'][$pane_id], $form_state, $form);
      }
    }
    return $form;
  }


  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($this->getVisiblePanes($form['#step_id']) as $pane_id => $pane) {
      if ($pane_id == 'custom_checkout_form') {
        $items = $this->order->getItems();
        foreach ($items AS $item) {
          $product_variation = $item->getPurchasedEntity();
          $product = $product_variation->getProduct();
          $wrapper_field = $product->get('field_checkout_form_wrapper');
          if (!$wrapper_field->isEmpty()) {
            $bundle = $wrapper_field->getValue()[0]['value'];
            $compound_pane_id = $pane_id . '_' . $product_variation->id();
            $pane->validatePaneForm($form[$compound_pane_id], $form_state, $form);
          }
        }
      }
      else {
        $pane->validatePaneForm($form[$pane_id], $form_state, $form);
      }
    }
    if ($this->hasSidebar($form['#step_id'])) {
      foreach ($this->getVisiblePanes('_sidebar') as $pane_id => $pane) {
        $pane->validatePaneForm($form['sidebar'][$pane_id], $form_state, $form);
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    foreach ($this->getVisiblePanes($form['#step_id']) as $pane_id => $pane) {
      if ($pane_id == 'custom_checkout_form') {
        $items = $this->order->getItems();
        foreach ($items AS $item) {
          $product_variation = $item->getPurchasedEntity();
          $product = $product_variation->getProduct();
          $wrapper_field = $product->get('field_checkout_form_wrapper');
          if (!$wrapper_field->isEmpty()) {
            $bundle = $wrapper_field->getValue()[0]['value'];
            $compound_pane_id = $pane_id . '_' . $product_variation->id();
            $pane->submitPaneForm($form[$compound_pane_id], $form_state, $form);
          }
        }
      }
      else {
        $pane->submitPaneForm($form[$pane_id], $form_state, $form);
      }
    }
    if ($this->hasSidebar($form['#step_id'])) {
      foreach ($this->getVisiblePanes('_sidebar') as $pane_id => $pane) {
        $pane->submitPaneForm($form['sidebar'][$pane_id], $form_state, $form);
      }
    }
  }

  public function afterBuild(array $element, FormStateInterface $form_state) {
    return $element;
  }

  public function processForm(array $form, FormStateInterface $form_state) {
    return $form; 
  }
}
