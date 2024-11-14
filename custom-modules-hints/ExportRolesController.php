<?php

namespace Drupal\code_mcit\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


/**
 * Returns responses for Code MCIT routes.
 */
class ExportRolesController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $roles = user_roles();
    // dd($roles);
    // Open a file for writing.
    $filename = 'website_roles.csv';
    $handle = fopen($filename, 'w');

    // Write header row.
    fputcsv($handle, ['rid', 'name']);

    // Loop through roles and write data.
    foreach ($roles as $rid => $role) {
      fputcsv($handle, [$rid, $role->label()]);
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

}
