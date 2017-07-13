<?php

namespace Drupal\commerce_custom_checkout_forms\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides specific access control for the node entity type.
 *
 * @EntityReferenceSelection(
 *   id = "default:checkout_form",
 *   label = @Translation("Checkout Form selection"),
 *   entity_types = {"checkout_form"},
 *   group = "default",
 *   weight = 1
 * )
 */
class CheckoutFormSelection extends DefaultSelection {
  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['target_bundles']['#title'] = $this->t('Checkout Forms');
    return $form;
  }
}
