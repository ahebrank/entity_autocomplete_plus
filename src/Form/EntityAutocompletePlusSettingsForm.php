<?php

namespace Drupal\entity_autocomplete_plus\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Entity autocomplete plus settings form.
 */
class EntityAutocompletePlusSettingsForm extends ConfigFormBase {

  /**
   * The renderer service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a EntityAutocompletePlusSettingsForm.
   *
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer service.
   */
  public function __construct(RendererInterface $renderer) {
    $this->renderer = $renderer;
  }

  /**
   * Injected services.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('renderer')
    );
  }

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
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return ['entity_autocomplete_plus.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $admin_configurations = $this->config('entity_autocomplete_plus.settings');
    $form['number_of_matches'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of matches'),
      '#default_value' => $admin_configurations->get('number_of_matches') ? $admin_configurations->get('number_of_matches') : 10,
      '#size' => 10,
      '#maxlength' => 10,
      '#description' => $this->t("Default number of matches/suggestions to return."),
      '#attributes' => [
        ' type' => 'number',
      ],
    ];

    $token_tree = [
      '#theme' => 'token_tree_link',
      '#token_types' => 'all',
    ];
    $rendered_token_tree = $this->renderer->render($token_tree);
    $form['token_string'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Default token string'),
      '#default_value' => $admin_configurations->get('token_string') ? $admin_configurations->get('token_string') : '',
      '#size' => 60,
      '#maxlength' => 60,
      '#description' => $this->t("Default token string to append to entity reference matcher suggestions. Make sure to override per-field when matching different entity types. @browse_link", [
        '@browse_link' => $rendered_token_tree,
      ]),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config_values = $form_state->getValues();
    $config_fields = [
      'token_string',
      'number_of_matches',
    ];
    $config = $this->config('entity_autocomplete_plus.settings');
    foreach ($config_fields as $config_field) {
      $config->set($config_field, $config_values[$config_field])
        ->save();
    }
    parent::submitForm($form, $form_state);
  }

}
