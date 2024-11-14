<?php

namespace Drupal\date_range_status;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Drupal\node\Entity\Node;

/**
 * Class NodeStatusFunction.
 */
class NodeStatusFunction extends AbstractExtension {

  /**
   * Custom twig functions.
   */
  public function getFunctions() {
    return [
      new TwigFunction('node_status', [$this, 'getNodeStatus']),
    ];
  }

  public function getNodeStatus($nid, $field_date) {
    $node_status = '';
    $node_status_class = '';
    $node = Node::load($nid);
    $expire_date = 0;
    $start_date = 0;
    $current_date = \Drupal::time()->getCurrentTime();
    
    $start_date = $node->get($field_date)->value ? strtotime($node->get($field_date)->value) : 0;
    $expire_date = $node->get($field_date)->end_value ? strtotime($node->get($field_date)->end_value) : 0;

    if ($node->isPublished()) {    
        if ($expire_date != 0 && $current_date < $start_date) {
            $node_status_class = 'status-later';
            $node_status = t('Later');
        } elseif ($expire_date != 0 && $current_date > $start_date && $current_date < $expire_date) {
            $node_status_class = 'status-open';
            $node_status = t('Open');
        } elseif ($expire_date != 0 && $current_date > $expire_date) {
            $node_status_class = 'status-closed';
            $node_status = t('Closed');
        }
    }
    return [
        'label' => $node_status,
        'label_class' => $node_status_class,
    ];
  }

}