<?php

namespace Drupal\commerce_custom_checkout_forms;

class CheckoutFormManager {
  protected $connection;
  public function __construct($entity_query, $entity_type_manager) {
    $this->entity_query = $entity_query;
    $this->entity_type_manager = $entity_type_manager;
  }
  public function getCheckoutFormWrapperByProductVariation($uid, $order_id, $product_variation_id) {
    $output = array();
    $query = $this->entity_query->get('checkout_form_wrapper');
    $query->condition('user_id', $uid);
    $query->condition('order_id', $order_id);
    $query->condition('product_variation_id', $product_variation_id);
    $result = $query->execute();      
    if (!empty($result)) {
      $output = $this->entity_type_manager->getStorage('checkout_form_wrapper')->loadMultiple($result);
      $output = array_shift($output);
    }
    return $output;
  }
  protected function getVariationsFromOrder($order) {
    $variations = array();
    $items = $order->getItems();
    foreach ($items AS $item) {
      $product_variation = $item->getPurchasedEntity();
      $variations[] = $product_variation->id(); 
    } 
    return $variations;
  }
  public function getPreviousProductVariationCheckout($order, $current_variation_id = FALSE) {
    $previous_variation_id = FALSE;
    $variations = $this->getVariationsFromOrder($order);
    if (!empty($current_variation_id)) {
      $key = array_search($current_variation_id, $variations);
      if (!empty($variations[$key + -1])) {
        // Return the next variation on the order.
        $previous_variation_id = $variations[$key + -1];
      }
    }
    else {
      // Return first key if we don't have a current step.
      $previous_variation_id = array_pop($variations);
    }
    return $previous_variation_id;
  }
  public function getNextProductVariationCheckout($order, $current_variation_id = FALSE) {
    $next_variation_id = FALSE;
    $variations = $this->getVariationsFromOrder($order);
    if (!empty($current_variation_id)) {
      $key = array_search($current_variation_id, $variations); 
      if (!empty($variations[$key + 1])) {
        // Return the next variation on the order.
        $next_variation_id = $variations[$key + 1];
      }
    }
    else {
      // Return first key if we don't have a current step.
      $next_variation_id = $variations[0];
    }
    return $next_variation_id;
  }
}
