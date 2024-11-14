<?php

declare(strict_types=1);

namespace Drupal\code_config\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Configure Site Settings settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'code_config_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['code_config.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    if ($langcode == 'en') {
      $message = $this->t('This setting will be saved for the English language. If you need to add Arabic settings, please visit the link for translation. <a href=":link">Translate site settings</a>', [':link' => Url::fromRoute('config_translation.item.overview.code_config.settings_form')->toString()]);
      $form['warning'] = [
        '#type' => 'markup',
        '#markup' => '<div class="messages messages--warning">' . $message . '</div>',
      ];
    }

    // Main Tabs.
    $form['tabs'] = [
      '#type' => 'vertical_tabs',
      '#default_tab' => 'edit-main',
    ];

    $form['#tree'] = TRUE;
    $form += $this->getsliderFormElements();
    $form += $this->getFooterFormElements();

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';
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
    $config = $this->config('code_config.settings');
    // Get form without tree.
    $values = $form_state->cleanValues()->getValues();

    // Make files permanent.
    $files_keys = [
      'front.slider.image',
      'footer.apps.andorid.android_image',
      'footer.apps.ios.ios_image',
    ];

    foreach ($files_keys as $key) {
      // Split each . as array key.
      $keys = explode('.', $key);
      $file = $values;
      foreach ($keys as $k) {
        $file = $file[$k];
      }
      if ($file) {
        self::mkPermanent($file);
      }
    }

    // Save settings.
    $skip = ["actions", "tabs"];
    foreach ($values as $key => $value) {
      if (!in_array($key, $skip)) {
        $config->set($key, $value);
      }
    }
    $config->save();
  }

  /**
   * Returns the main form elements for the slider form.
   *
   * @return array
   *   An array of form elements.
   */
  public function getsliderFormElements(): array {
    $formDefaultValues = $this->config('code_config.settings')->get('front');
    $form['front'] = [
      '#type' => 'details',
      '#title' => $this->t('Homepage Settings'),
      '#group' => 'tabs',
    ];
    $form['front']['slider'] = [
      '#type'  => 'details',
      '#open'  => FALSE,
      '#title' => $this->t('Slider Details'),
    ];
    $form['front']['slider']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $formDefaultValues['slider']['title'] ?? '',
    ];
    $form['front']['slider']['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#description' => t('upload image svg,jpg,png,jpeg'),
      '#upload_location' => 'public://slider_image/',
      '#upload_validators' => [
        'file_validate_extensions' => ['svg jpg png jpeg'],
      ],
      '#default_value' => $formDefaultValues['slider']['image'],
    ];
    $form['front']['slider']['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $formDefaultValues['slider']['description'],
    ];
    $form['front']['slider']['button_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button title'),
      '#default_value' => $formDefaultValues['slider']['button_text'],
    ];
    $form['front']['slider']['button_url'] = [
      '#type'  => 'textfield',
      '#title' => $this->t('button url'),
      '#default_value' => $formDefaultValues['slider']['button_url'],
    ];
    // Vision Inputs.
    $form['front']['vision'] = [
      '#type'  => 'details',
      '#open'  => FALSE,
      '#title' => $this->t('Vision Details'),
    ];
    $form['front']['vision']['vision_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $formDefaultValues['vision']['vision_title'],
    ];
    $form['front']['vision']['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $formDefaultValues['vision']['description'],
    ];
    // Statistics.
    $form['front']['statistics'] = [
      '#type'  => 'details',
      '#open'  => FALSE,
      '#title' => $this->t('Statistics'),
    ];
    $form['front']['statistics']['startups'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Startups'),
      '#default_value' => $formDefaultValues['statistics']['startups'],
    ];
    $form['front']['statistics']['persons'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Persons'),
      '#default_value' => $formDefaultValues['statistics']['persons'],
    ];
    $form['front']['statistics']['prizes'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Prizes'),
      '#default_value' => $formDefaultValues['statistics']['prizes'],
    ];
    return $form;
  }

  /**
   * Returns the main form elements for the settings form.
   *
   * @return array
   *   An array of form elements.
   */
  public function getFooterFormElements(): array {
    $formDefaultValues = $this->config('code_config.settings')->get('footer');
    $form = [];
    $form['footer'] = [
      '#type' => 'details',
      '#title' => $this->t('Footer Settings'),
      '#group' => 'tabs',
    ];
    $form['footer']['apps'] = [
      '#type' => 'details',
      '#title' => $this->t('Apps'),
      '#open' => FALSE,
    ];
    $form['footer']['apps']['andorid'] = [
      '#type' => 'details',
      '#title' => $this->t('Android'),
      '#open' => FALSE,
    ];
    $form['footer']['apps']['andorid']['android_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#description' => t('upload image svg,jpg,png,jpeg'),
      '#upload_location' => 'public://slider_image/',
      '#upload_validators' => [
        'file_validate_extensions' => ['svg jpg png'],
      ],
      '#default_value' => $formDefaultValues['apps']['andorid']['android_image'],
    ];
    $form['footer']['apps']['andorid']['android_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Android app link'),
      '#default_value' => $formDefaultValues['apps']['andorid']['android_url'] ?? '',
    ];
    $form['footer']['apps']['ios'] = [
      '#type' => 'details',
      '#title' => $this->t('IOS'),
      '#open' => FALSE,
    ];
    $form['footer']['apps']['ios']['ios_image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#description' => t('upload image svg,jpg,png,jpeg'),
      '#upload_location' => 'public://slider_image/',
      '#upload_validators' => [
        'file_validate_extensions' => ['svg jpg png'],
      ],
      '#default_value' => $formDefaultValues['apps']['ios']['ios_image'],
    ];
    $form['footer']['apps']['ios']['ios_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('IOS app link'),
      '#default_value' => $formDefaultValues['apps']['ios']['ios_url'] ?? '',
    ];
    $form['footer']['social_media'] = [
      '#type' => 'details',
      '#title' => $this->t('Social Media'),
      '#open' => FALSE,
    ];
    $form['footer']['social_media']['x'] = [
      '#type' => 'textfield',
      '#title' => $this->t('X'),
      '#default_value' => $formDefaultValues['social_media']['x'] ?? '',
    ];
    $form['footer']['copyrights'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Copy Rights'),
      '#default_value' => $formDefaultValues['copyrights'] ?? '',
    ];
    return $form;
  }

  /**
   * Make a file permanent.
   *
   * @param array $file
   *   The file array.
   */
  public function mkPermanent(array $file): void {
    $permanent_file = $file;
    if (is_array($permanent_file)) {
      if (isset($permanent_file[0])) {
        $permanent_file_id = $permanent_file[0];
        $real_file = File::load($permanent_file_id);
        if ($real_file != NULL && $real_file->isTemporary()) {
          $real_file->setPermanent();
          $real_file->save();
        }
      }
    }
  }

}
