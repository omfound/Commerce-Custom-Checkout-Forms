<?php

namespace Drupal\commerce_custom_checkout_forms;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Checkout form wrapper entities.
 *
 * @ingroup commerce_custom_checkout_forms
 */
class CheckoutFormWrapperListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Checkout form wrapper ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormWrapper */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.checkout_form_wrapper.edit_form', [
          'checkout_form_wrapper' => $entity->id(),
        ]
      )
    );
    return $row + parent::buildRow($entity);
  }

}
