<?php

// Get all Owner fields reference EntityQuery
// You can use all fields by owner for example :
// 1 - user name 

$query = \Drupal::entityQuery('node')
        ->condition('type', 'article') 
        ->condition('uid.entity.name', 'admin')
        ->execute();

// 2 - If you have a field let's say called `field_full_name`
    $full_name = 'Mohammed';
        
    $query = \Drupal::entityQuery('node')
            ->condition('type', 'article') 
            ->condition('uid.entity.field_full_name',  '%' . $full_name . '%', 'LIKE')
            ->execute();