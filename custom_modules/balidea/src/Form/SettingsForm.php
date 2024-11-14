<?php

namespace Drupal\balidea\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Balidea settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'balidea_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['balidea.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['number'] = [
      '#type' => 'number',
      '#title' => $this->t('Number'),
      '#default_value' => $this->config('balidea.settings')->get('number'),
    ];
    $form['text_en'] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Text in English'),
      '#default_value' => $this->config('balidea.settings')->get('text_en'),
    ];
    $form['text_es'] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Text in Spanish'),
      '#default_value' => $this->config('balidea.settings')->get('text_es'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('balidea.settings');
    $skip = array("submit", "form_build_id", "form_token", "form_id", "op", "footer_contact_us", "no_result_search", "services_inquery", "comment_message", "voice_instructions");
    foreach ($form_state->getValues() as $key => $value) {
      if (!in_array($key, $skip)) {
        $config->set($key, $value);
      }
    }
    $config->save();
  }

}
