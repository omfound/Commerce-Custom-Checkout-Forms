<?php

/**
 * @file
 * Contains checkout_form_wrapper.page.inc.
 *
 * Page callback for Checkout form wrapper entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Checkout form wrapper templates.
 *
 * Default template: checkout_form_wrapper.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_checkout_form_wrapper(array &$variables) {
  // Fetch CheckoutFormWrapper Entity Object.
  $checkout_form_wrapper = $variables['elements']['#checkout_form_wrapper'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
