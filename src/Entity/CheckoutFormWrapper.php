<?php

namespace Drupal\commerce_custom_checkout_forms\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Checkout form wrapper entity.
 *
 * @ingroup commerce_custom_checkout_forms
 *
 * @ContentEntityType(
 *   id = "checkout_form_wrapper",
 *   label = @Translation("Checkout form wrapper"),
 *   bundle_label = @Translation("Checkout form wrapper type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_custom_checkout_forms\CheckoutFormWrapperListBuilder",
 *     "views_data" = "Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormWrapperViewsData",
 *     "translation" = "Drupal\commerce_custom_checkout_forms\CheckoutFormWrapperTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormWrapperForm",
 *       "add" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormWrapperForm",
 *       "edit" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormWrapperForm",
 *       "delete" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormWrapperDeleteForm",
 *     },
 *     "access" = "Drupal\commerce_custom_checkout_forms\CheckoutFormWrapperAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_custom_checkout_forms\CheckoutFormWrapperHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "checkout_form_wrapper",
 *   data_table = "checkout_form_wrapper_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer checkout form wrapper entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/checkout_form_wrapper/{checkout_form_wrapper}",
 *     "add-page" = "/admin/commerce/checkout_form_wrapper/add",
 *     "add-form" = "/admin/commerce/checkout_form_wrapper/add/{checkout_form_wrapper_type}",
 *     "edit-form" = "/admin/commerce/checkout_form_wrapper/{checkout_form_wrapper}/edit",
 *     "delete-form" = "/admin/commerce/checkout_form_wrapper/{checkout_form_wrapper}/delete",
 *     "collection" = "/admin/commerce/checkout_form_wrapper",
 *   },
 *   bundle_entity_type = "checkout_form_wrapper_type",
 *   field_ui_base_route = "entity.checkout_form_wrapper_type.edit_form"
 * )
 */
class CheckoutFormWrapper extends ContentEntityBase implements CheckoutFormWrapperInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the CheckoutForm entity.'))
      ->setReadOnly(TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the CheckoutForm entity.'))
      ->setReadOnly(TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Checkout form wrapper entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['order_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Order'))
      ->setDescription(t('The parent order.'))
      ->setSetting('target_type', 'commerce_order')
      ->setReadOnly(TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Checkout form wrapper is published.'))
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of ContentEntityExample entity.'));

    return $fields;
  }

}
