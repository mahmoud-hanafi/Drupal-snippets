<?php

// Set created hijri date.
$created = $node->getCreatedTime();
// using convertToHijri($timestamp, $format, $is_indian = 0) from HijriFormatManager service in hijri_format module.
$created_hijri = \Drupal::service('hijri_format.manager')->convertToHijri($created, 'd M Y');
$variables['created_hijri'] = $created_hijri;