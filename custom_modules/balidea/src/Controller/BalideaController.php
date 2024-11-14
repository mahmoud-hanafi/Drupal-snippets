<?php

namespace Drupal\balidea\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Balidea routes.
 */
class BalideaController extends ControllerBase {
  
  /**
   * get data from configuration form.
   */
  public function get_data() {

    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $config = \Drupal::config('balidea.settings');
    $number = $config->get('number');
    $text_en = $config->get('text_en');
    $text_es = $config->get('text_es');
    $data = [
      'number' => $number,
      'text_en' => $text_en,
      'text_es' => $text_es,
      'langcode' => $langcode,
    ];
    return $data;
  }
  
  /**
   * Builds the response.
   */
  public function build() {

    return array(
  		'#theme' => 'form_data',
  		'#items' => $this->get_data(),
      '#cache' => [
        'max-age' => 0,
      ],
  	);

  }

}
