<?php

namespace Drupal\commerce_custom_checkout_forms;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Checkout form entities.
 *
 * @ingroup commerce_custom_checkout_forms
 */
class CheckoutFormListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Checkout form ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_custom_checkout_forms\Entity\CheckoutForm */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.checkout_form.edit_form', [
          'checkout_form' => $entity->id(),
        ]
      )
    );
    return $row + parent::buildRow($entity);
  }

}
