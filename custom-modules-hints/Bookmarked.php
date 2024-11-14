<?php

// Query to check if the node is bookmarked or not
\Drupal::database()->query("SELECT * FROM `flagging` WHERE `entity_id` = $nid")->fetchfield();

// Render all Bookmarked Nodes Programmatically.
function data(){
    $nids= \Drupal::database()->query("SELECT `entity_id` FROM `flagging` WHERE `flag_id` = 'bookmarks'")->fetchAllKeyed(0,0);
    $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);
    $data = array();
    foreach ($nodes as $node) {
      $title = $node->get('title')->value;
      $data[] = array(
       "id" => $node->id(),
       "title" => $title,
       "url" => $node->toUrl()->toString(),
      );
    }
    return $data;
}