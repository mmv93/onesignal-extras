<?php

namespace Drupal\onesignal_extras\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Configuration form for OneSignal extras.
 *
 * Class OSConfigForm.
 */
class OSConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'onesignal_extras.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'os_extras_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('onesignal_extras.settings');

    $form['icon'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Notification icon.'),
      '#upload_location' => 'public://os-extras',
      '#progress_message' => $this->t('Please wait...'),
      '#multiple' => FALSE,
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg webp ico cur bmp'],
        /*'file_validate_image_resolution' => ['192x192'],*/
      ],
      '#description' => $this->t('Image resolution should be 192x192 pixels. Supported file types: gif, png, jpg, webp, ico, cur, bmp. Gif animation is not supported.')
    ];

    $fid = $config->get('icon');
    if ($fid) {
      $image_file = File::load($fid);
      if ($image_file instanceof File) {
        $form['icon']['#default_value'] = ['target_id' => $fid];
      }
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $uploads = $form_state->getValue('icon');
    if (is_array($uploads)) {
      $fid = reset($uploads);
    }

    parent::submitForm($form, $form_state);
    $this->config('onesignal_extras.settings')
      ->set('icon', $fid)
      ->save();
  }

}
