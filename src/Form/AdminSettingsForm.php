<?php
/**
 * @file
 * Contains Drupal\gacsp\Form\SettingsForm.
 */

namespace Drupal\gacsp\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for editing TVM API Proxy settings.
 */
class AdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gacsp_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'gacsp.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('gacsp.settings');

    $form['tracking_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Web Property Tracking ID'),
      '#description' => $this->t('Tracking ID in the format "UA-xxxxxxx-y"'),
      '#placeholder' => 'UA-xxxxxxxx-y',
      '#maxlength' => 20,
      '#size' => '20',
      '#default_value' => $config->get('tracking_id'),
    ];

    $form['add_default_commands'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add default analytics commands'),
      '#description' => $this->t('Add \'create\' and \'send pageview\' commands to enable tracking.  Disable if another module will be providing these commands.'),
      '#default_value' => $config->get('add_default_commands'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $property = $form_state->getValue('tracking_id');
    if (!empty($property) && !preg_match('/^UA-\d+-\d+$/', $property)) {
      $form_state->setErrorByName('tracking_id', t('The provided Tracking ID is not valid.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('gacsp.settings')
      ->set('tracking_id', $form_state->getValue('tracking_id'))
      ->set('add_default_commands', $form_state->getValue('add_default_commands'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
