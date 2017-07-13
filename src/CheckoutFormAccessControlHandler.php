<?php

namespace Drupal\commerce_custom_checkout_forms;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Checkout form entity.
 *
 * @see \Drupal\commerce_custom_checkout_forms\Entity\CheckoutForm.
 */
class CheckoutFormAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished checkout form entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published checkout form entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit checkout form entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete checkout form entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add checkout form entities');
  }

}
