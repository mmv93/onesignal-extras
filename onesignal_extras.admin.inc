<?php

/**
 * @file
 * Configuration form for the One Signal extras module.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * @see onesignal_extras_form_node_type_form_alter()
 */
function _onesignal_extras_form_node_type_form_alter(array &$form, FormStateInterface $form_state) {
  $config = \Drupal::config('onesignal_extras.settings');

  /** @var \Drupal\node\NodeTypeInterface $type */
  $type = $form_state->getFormObject()->getEntity();

  $form['onesignal_extras'] = [
    '#type' => 'details',
    '#title' => t('One Signal settings'),
    '#weight' => 36,
    '#group' => 'additional_settings',
    '#description' => t('One Signal push notifications settings'),
  ];

  $form['onesignal_extras']['onesignal_extras_enable'] = [
    '#type' => 'checkbox',
    '#title' => t('Enable One signal publishing for this content type'),
    '#default_value' => $type->getThirdPartySetting('onesignal_extras', 'onesignal_extras_enable', $config->get('default_onesignal_extras_enable')),
  ];

  $form['onesignal_extras']['onesignal_extras_preview'] = [
    '#type' => 'checkbox',
    '#title' => t('Enable live preview for notification.'),
    '#states' => [
      'enabled' => [
        ':input[name="onesignal_extras_enable"]' => ['checked' => TRUE],
      ],
    ],
    '#default_value' => $type->getThirdPartySetting('onesignal_extras', 'onesignal_extras_preview', $config->get('default_onesignal_extras_preview')),
  ];

  $form['#entity_builders'][] = 'onesignal_extras_form_node_type_form_builder';
}
