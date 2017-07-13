<?php

namespace Drupal\commerce_custom_checkout_forms\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Checkout form entities.
 */
class CheckoutFormViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
