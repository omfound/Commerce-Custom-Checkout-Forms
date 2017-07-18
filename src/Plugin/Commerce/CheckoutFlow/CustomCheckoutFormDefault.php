<?php

namespace Drupal\commerce_custom_checkout_forms\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowWithPanesBase;
use Drupal\commerce_checkout\CheckoutPaneManager;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\commerce\Response\NeedsRedirectException;

/**
 * Provides the default multistep checkout flow.
 *
 * @CommerceCheckoutFlow(
 *   id = "custom_checkout_form_default",
 *   label = "Custom Checkout Form Default",
 * )
 */
class CustomCheckoutFormDefault extends CheckoutFlowWithPanesBase {

  public function __construct(array $configuration, $pane_id, $pane_definition, EntityTypeManagerInterface $entity_type_manager, EventDispatcherInterface $event_dispatcher, RouteMatchInterface $route_match, CheckoutPaneManager $pane_manager, $checkout_form_manager, $current_user) {
    $this->checkout_form_manager = $checkout_form_manager;
    $this->current_user = $current_user;
    parent::__construct($configuration, $pane_id, $pane_definition, $entity_type_manager, $event_dispatcher, $route_match, $pane_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pane_id, $pane_definition) {
    return new static(
      $configuration,
      $pane_id,
      $pane_definition,
      $container->get('entity_type.manager'),
      $container->get('event_dispatcher'),
      $container->get('current_route_match'),
      $container->get('plugin.manager.commerce_checkout_pane'),
      $container->get('commerce_custom_checkout_forms.checkout_form_manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getSteps() {
    // Note that previous_label and next_label are not the labels
    // shown on the step itself. Instead, they are the labels shown
    // when going back to the step, or proceeding to the step.
    return [
      'login' => [
        'label' => $this->t('Login'),
        'previous_label' => $this->t('Go back'),
        'has_sidebar' => FALSE,
      ],
      'required_forms' => [
        'label' => $this->t('Required forms'),
        'has_sidebar' => TRUE,
        'previous_label' => $this->t('Go Back'),
        'next_label' => $this->t('Continue'),
      ],
      'order_information' => [
        'label' => $this->t('Order information'),
        'has_sidebar' => TRUE,
        'previous_label' => $this->t('Go back'),
        'next_label' => $this->t('Continue'),
      ],
      'review' => [
        'label' => $this->t('Review'),
        'next_label' => $this->t('Continue to review'),
        'previous_label' => $this->t('Go back'),
        'has_sidebar' => TRUE,
      ],
    ] + parent::getSteps();
  }
  public function buildForm(array $form, FormStateInterface $form_state, $step_id = NULL) {
    if ($step_id == 'required_forms') {
      $product_variation_id = \Drupal::routeMatch()->getParameter('commerce_product_variation');
      if (!empty($product_variation_id)) {
        $product_variation = $this->entityTypeManager->getStorage('commerce_product_variation')->load($product_variation_id); 
      }
      else {
        $items = $this->order->getItems();
        $product_variation = $items[0]->getPurchasedEntity();
      }
      $product = $product_variation->getProduct();       
      $wrapper_field = $product->get('field_checkout_form_wrapper');
      if (!$wrapper_field->isEmpty()) {
        $bundle = $wrapper_field->getValue()[0]['value'];
        $checkout_form_wrapper = $this->checkout_form_manager->getCheckoutFormWrapperByProductVariation($this->current_user->id(), $this->order->id(), $product_variation->id()); 
        $storage = $form_state->getStorage();
        $storage['required_form_storage'] = [
          'bundle' => $bundle,
          'product_variation' => $product_variation,
          'product' => $product,
          'checkout_form_wrapper' => $checkout_form_wrapper
        ];
        $form_state->setStorage($storage);
      }
    }
    $form = parent::buildForm($form, $form_state, $step_id);
    return $form;
  }

  public function afterBuild(array $element, FormStateInterface $form_state) {
    return $element;
  }

  public function processForm(array $form, FormStateInterface $form_state) {
    return $form; 
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    if ($form['#step_id'] == 'required_forms') {
      $variation_id = \Drupal::routeMatch()->getParameter('commerce_product_variation');
      if (empty($variation_id)) {
        $variation_id = $this->order->getItems()[0]->getPurchasedEntity()->id();
      }
      $next_variation_id = $this->checkout_form_manager->getNextProductVariationCheckout($this->order, $variation_id);
      if (!empty($next_variation_id)) {
        throw new NeedsRedirectException(Url::fromRoute('commerce_custom_checkout_forms.form_extra', [
          'commerce_order' => $this->order->id(),
          'step' => 'required_forms',
          'commerce_product_variation' => $next_variation_id
        ])->toString());
      }
    }
  } 

  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $steps = $this->getVisibleSteps();
    $previous_step_id = $this->getPreviousStepId($form['#step_id']);
    if ($form['#step_id'] == 'required_forms' || $previous_step_id  == 'required_forms') {
      $variation_id = \Drupal::routeMatch()->getParameter('commerce_product_variation');
      if ($form['#step_id'] == 'required_forms') {
        $label = t('Previous Required Form');
        if (empty($variation_id)) {
          $variation_id = $this->order->getItems()[0]->getPurchasedEntity()->id();
        }
      }
      if ($previous_step_id  == 'required_forms') {
        $label = t('Back to Required Forms');
      }
      $previous_variation_id = $this->checkout_form_manager->getPreviousProductVariationCheckout($this->order, $variation_id);
      if (!empty($previous_variation_id)) {
        $actions['next']['#suffix'] = Link::createFromRoute($label, 'commerce_custom_checkout_forms.form_extra', [
          'commerce_order' => $this->order->id(),
          'step' => 'required_forms',
          'commerce_product_variation' => $previous_variation_id 
        ])->toString(); 
      }
    }
    return $actions;
  }
}
