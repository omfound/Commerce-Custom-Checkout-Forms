<?php

namespace Drupal\commerce_custom_checkout_forms\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Checkout form entity.
 *
 * @ingroup commerce_custom_checkout_forms
 *
 * @ContentEntityType(
 *   id = "checkout_form",
 *   label = @Translation("Checkout form"),
 *   bundle_label = @Translation("Checkout form type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\commerce_custom_checkout_forms\CheckoutFormListBuilder",
 *     "views_data" = "Drupal\commerce_custom_checkout_forms\Entity\CheckoutFormViewsData",
 *     "translation" = "Drupal\commerce_custom_checkout_forms\CheckoutFormTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormForm",
 *       "add" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormForm",
 *       "edit" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormForm",
 *       "delete" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormDeleteForm",
 *     },
 *     "inline_form" = "Drupal\commerce_custom_checkout_forms\Form\CheckoutFormInlineForm",
 *     "access" = "Drupal\commerce_custom_checkout_forms\CheckoutFormAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\commerce_custom_checkout_forms\CheckoutFormHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "checkout_form",
 *   data_table = "checkout_form_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer checkout form entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "bundle" = "type",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/commerce/checkout_form/{checkout_form}",
 *     "add-page" = "/admin/commerce/checkout_form/add",
 *     "add-form" = "/admin/commerce/checkout_form/add/{checkout_form_type}",
 *     "edit-form" = "/admin/commerce/checkout_form/{checkout_form}/edit",
 *     "delete-form" = "/admin/commerce/checkout_form/{checkout_form}/delete",
 *     "collection" = "/admin/commerce/checkout_form",
 *   },
 *   bundle_entity_type = "checkout_form_type",
 *   field_ui_base_route = "entity.checkout_form_type.edit_form",
 *   common_reference_target = TRUE,
 * )
 */
class CheckoutForm extends ContentEntityBase implements CheckoutFormInterface {

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
      ->setDisplayConfigurable('form', TRUE);

    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the CheckoutForm entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User Name'))
      ->setDescription(t('The Name of the associated user.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default');
      /*
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'author',
        'weight' => -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => '',
        ),
        'weight' => -3,
      ));*/

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of ContentEntityExample entity.'));

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Checkout form is published.'))
      ->setDefaultValue(TRUE);

    return $fields;
  }

}
