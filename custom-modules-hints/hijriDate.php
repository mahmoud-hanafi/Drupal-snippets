<?php

// first should use hijri_format Contrib Module 
/**
 * Implements hook_preprocess_HOOK() for node.html.twig.
 */
function custom_theme_preprocess_node(&$variables)
{
    $node = $variables['node'];
    // Set created hijri date.
    $created = $node->getCreatedTime();
    // using convertToHijri($timestamp, $format, $is_indian = 0) from HijriFormatManager service in hijri_format module.
    $created_hijri = \Drupal::service('hijri_format.manager')->convertToHijri($created, 'd M Y');
    $variables['created_hijri'] = $created_hijri;
}