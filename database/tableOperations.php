// update an existing field at a table in database
$database = \Drupal::service('database');

$count_update = $database->update('newsletter_files_downloads_count')
->expression('count', 'count + 1')
->condition('fid', $fid)
->execute();


// Adding field values to table
$database = \Drupal::service('database');

$database->insert('newsletter_files_downloads_count')
->fields([
'fid' => $fid,
'count' => 1,
])->execute();

//Alter Existing table in database
**
* alter file_usage table by adding column to store the times count of file downlaoding
*/
function custom_update_9001(){
    $spec = [
        'type' => 'int',
        'description' => "New Col to store the count of file downloads count",
        'length' => 20,
        'not null' => TRUE,
        'default' => 0,
    ];
    $schema = Database::getConnection()->schema();
    $schema->addField('file_usage', 'downloads_number', $spec);

}

// copy data from field to another using one sql query in terminal
$sql = $database->query("
  INSERT INTO user__field_mobile_number( bundle, deleted, entity_id, revision_id, langcode, delta, field_mobile_number_value )  
  SELECT bundle, deleted, entity_id, revision_id, langcode, delta, field_mobile_value
  FROM user__field_mobile")->execute();


