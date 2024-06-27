<?php

// 1 - Database select 

$result = $query->countQuery()->execute()->fetchField();

// 2 - EntityQuery

$result = $query->count()->execute();

// MySQL : SQLSTATE[HY000]: General error: 1364 Field 'field_name' doesn't have a default value
// = ALTER TABLE YourTable MODIFY column_b VARCHAR(255) DEFAULT NULL;