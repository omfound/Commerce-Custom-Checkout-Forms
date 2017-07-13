<?php

namespace Drupal\commerce_custom_checkout_forms\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Checkout form wrapper entities.
 *
 * @ingroup commerce_custom_checkout_forms
 */
interface CheckoutFormWrapperInterface extends  ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Checkout form wrapper name.
   *
   * @return string
   *   Name of the Checkout form wrapper.
   */
  public function getName();

  /**
   * Sets the Checkout form wrapper name.
   *
   * @param string $name
   *   The Checkout form wrapper name.
   *
   * @return \Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormWrapperInterface
   *   The called Checkout form wrapper entity.
   */
  public function setName($name);

  /**
   * Gets the Checkout form wrapper creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Checkout form wrapper.
   */
  public function getCreatedTime();

  /**
   * Sets the Checkout form wrapper creation timestamp.
   *
   * @param int $timestamp
   *   The Checkout form wrapper creation timestamp.
   *
   * @return \Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormWrapperInterface
   *   The called Checkout form wrapper entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Checkout form wrapper published status indicator.
   *
   * Unpublished Checkout form wrapper are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Checkout form wrapper is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Checkout form wrapper.
   *
   * @param bool $published
   *   TRUE to set this Checkout form wrapper to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormWrapperInterface
   *   The called Checkout form wrapper entity.
   */
  public function setPublished($published);

}
