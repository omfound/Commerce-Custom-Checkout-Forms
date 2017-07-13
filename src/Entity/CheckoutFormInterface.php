<?php

namespace Drupal\commerce_custom_checkout_forms\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Checkout form entities.
 *
 * @ingroup commerce_custom_checkout_forms
 */
interface CheckoutFormInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Checkout form name.
   *
   * @return string
   *   Name of the Checkout form.
   */
  public function getName();

  /**
   * Sets the Checkout form name.
   *
   * @param string $name
   *   The Checkout form name.
   *
   * @return \Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormInterface
   *   The called Checkout form entity.
   */
  public function setName($name);

  /**
   * Gets the Checkout form creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Checkout form.
   */
  public function getCreatedTime();

  /**
   * Sets the Checkout form creation timestamp.
   *
   * @param int $timestamp
   *   The Checkout form creation timestamp.
   *
   * @return \Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormInterface
   *   The called Checkout form entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Checkout form published status indicator.
   *
   * Unpublished Checkout form are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Checkout form is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Checkout form.
   *
   * @param bool $published
   *   TRUE to set this Checkout form to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormInterface
   *   The called Checkout form entity.
   */
  public function setPublished($published);

}
