<?php

namespace Drupal\code_mcit\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;


/**
 * Returns responses for Code MCIT routes.
 */
class ExportContentTypesController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    // $roles = user_roles();
    
    $entityTypeManager = \Drupal::entityTypeManager();
    $content_type_names = self::get_all_content_type_names($entityTypeManager);
    // dd($roles);
    // Open a file for writing.
    $filename = 'website_content_types.csv';
    $handle = fopen($filename, 'w');

    // Write header row.
    fputcsv($handle, ['rid', 'name']);

    // Loop through roles and write data.
    foreach ($content_type_names as $rid => $type) {
      fputcsv($handle, [$rid, $type]);
    }

    // Close the file.
    fclose($handle);
    $headers = [
      'Content-Type' => 'text/csv',
      'Content-Description' => 'File Download',
      'Content-Disposition' => 'attachment; filename=' . $filename
    ];
    return new BinaryFileResponse($filename, 200, $headers, true );
  
  }

  /**
   * Get all content type names.
   *
   * @return array An array containing all content type names.
   */
  public function get_all_content_type_names(EntityTypeManagerInterface $entityTypeManager) {
    $content_type_storage = $entityTypeManager->getStorage('node_type');
    $content_types = $content_type_storage->loadMultiple();
  
    $content_type_names = [];
    foreach ($content_types as $content_type) {
      $content_type_names[] = $content_type->label();
    }
  
    return $content_type_names;
  }

}
