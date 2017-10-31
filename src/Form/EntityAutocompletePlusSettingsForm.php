<?php

namespace Drupal\entity_autocomplete_plus\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EntityAutocompletePlusSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_autocomplete_plus_admin_configuration';
  }

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   * An array of configuration object names that are editable if called in
   * conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['entity_autocomplete_plus.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $admin_configurations = $this->config('entity_autocomplete_plus.settings');
    $form['token_string'] = array(
      '#type' => 'textfield',
      '#title' => t('Append token string'),
      '#default_value' => $admin_configurations->get('token_string') ? $admin_configurations->get('token_string') : '',
      '#size' => 60,
      '#maxlength' => 60,
      '#description' => t("Token string to include in entity reference matcher."),
    );
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config_values = $form_state->getValues();
    $config_fields = array(
      'token_string',
    );
    $config = $this->config('entity_autocomplete_plus.settings');
    foreach ($config_fields as $config_field) {
      $config->set($config_field, $config_values[$config_field])
          ->save();
    }
    parent::submitForm($form, $form_state);
  }

}