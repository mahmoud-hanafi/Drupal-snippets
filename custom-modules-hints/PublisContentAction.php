<?php

namespace Drupal\custom_module\Plugin\Action;

use Drupal\views_bulk_operations\Action\ViewsBulkOperationsActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\Entity\Node;

// add an acction to vbo to publish content

/**
 * Action to change moderation status.
 *
 * @Action(
 *   id = "publish_reviewed_content",
 *   label = "Publish reviewed content",
 *   type = "",
 *   confirm = TRUE,
 *   requirements = {
 *     "_permission" = "use editorial transition publish",
 *   },
 * )
 */
class PublisContentAction extends ViewsBulkOperationsActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    
    //change moderation state for the entity
    if ($entity->get('moderation_state')->getValue() != 'published') {
        $title = $entity->getTitle();
        $entity->set('moderation_state', 'published');
        $entity->save();
        \Drupal::messenger()->addMessage(t("@title node has been published successfully", ['@title' => $title]));
    }
    
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, ?AccountInterface $account = NULL, $return_as_object = FALSE) {
    return $object->access('view', $account, $return_as_object);
  }


}