<?php
// Database select to get all Flagged entities by current user

$query = \Drupal::database()
    ->select('flagging', 'f')
    ->fields('f', array('entity_id'))
    ->condition('flag_id', 'FLAG_ID')
    ->condition('uid', 'USER_TARGET_UID');

$result = $query->execute()->fetchAllKeyed(0, 0);