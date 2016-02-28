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

    $form['add_default_commands'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add default analytics commands'),
      '#description' => $this->t('Disable if another module will be providing these commands.'),
      '#default_value' => $config->get('add_default_commands'),
    ];

    $form['default_config'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Default Options'),
      '#states' => [
        'visible' => [
          ':input[data-drupal-selector="edit-add-default-commands"]' => ['checked' => TRUE],
        ],
      ],
    ];
    $form['default_config']['tracking_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Web Property Tracking ID'),
      '#description' => $this->t('Tracking ID in the format "UA-xxxxxxx-y"'),
      '#placeholder' => 'UA-xxxxxxxx-y',
      '#maxlength' => 20,
      '#size' => '20',
      '#default_value' => $config->get('tracking_id'),
    ];
    $form['default_config']['send_pageview'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Send Pageview Event'),
      '#default_value' => $config->get('send_pageview'),
    ];

    $form['default_config']['plugins'] = [
      '#type' => 'fieldset',
      '#title' => 'Plugins',
      '#tree' => TRUE,
    ];
    $form['default_config']['plugins']['linkid'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enhanced Link Attribution'),
      '#description' => $this->t('Improve the accuracy of your In-Page Analytics report by automatically differentiating between multiple links to the same URL on a single page by using link element IDs (<a href=":url">Documentation</a>).', [
        ':url' => 'https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution',
      ]),
      '#default_value' => $config->get('plugins.linkid'),
    ];
    $form['default_config']['plugins']['displayfeatures'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display Features'),
      '#description' => $this->t('Enable Advertising Features in Google Analytics, such as Remarketing, Demographics and Interest Reporting, and more (<a href=":url">Documentation</a>).<br/> This option can also be enabled through your property settings.', [
        ':url' => 'https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-link-attribution',
      ]),
      '#default_value' => $config->get('plugins.displayfeatures'),
    ];
    $form['default_config']['plugins']['linker'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Linker'),
      '#description' => $this->t('Enable cross-domain tracking (<a href=":url">Documentation</a>).', [
        ':url' => 'https://developers.google.com/analytics/devguides/collection/analyticsjs/linker',
      ]),
      '#default_value' => $config->get('plugins.linker.enable'),
    ];
    $form['default_config']['plugins']['linker_domains'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Linker Domains'),
      '#description' => $this->t('A comma separated list of domains.'),
      '#default_value' => implode(', ', $config->get('plugins.linker.domains')),
      '#states' => [
        'visible' => [
          ':input[data-drupal-selector="edit-plugins-linker"]' => ['checked' => TRUE],
        ],
      ],
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
      ->set('add_default_commands', $form_state->getValue('add_default_commands'))
      ->set('tracking_id', $form_state->getValue('tracking_id'))
      ->set('send_pageview', $form_state->getValue('send_pageview'))
      ->set('plugins.linkid', $form_state->getValue(['plugins', 'linkid']))
      ->set('plugins.displayfeatures', $form_state->getValue(['plugins', 'displayfeatures']))
      ->set('plugins.linker.enable', $form_state->getValue(['plugins', 'linker']))
      ->set('plugins.linker.domains', preg_split('/, ?/', $form_state->getValue(['plugins', 'linker_domains'])))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
