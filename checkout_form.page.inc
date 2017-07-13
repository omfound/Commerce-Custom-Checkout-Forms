<?php

/**
 * @file
 * Contains checkout_form.page.inc.
 *
 * Page callback for Checkout form entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Checkout form templates.
 *
 * Default template: checkout_form.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_checkout_form(array &$variables) {
  // Fetch CheckoutForm Entity Object.
  $checkout_form = $variables['elements']['#checkout_form'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
