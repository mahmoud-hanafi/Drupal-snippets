<?php
// in banckruptcy and date_range_status

// Creating a Custom Views Field
// To create a custom views field go first to the .module file and implement the views related hooks there.

/**
 * Implements hook_views_data_alter().
 */
function moduleName_views_data_alter(array &$data) {
  $data['node']['node_type_flagger'] = array(
    'title' => t('Node type flagger'),
    'group' => t('Content'),
    'field' => array(
      'title' => t('Node type flagger'),
      'help' => t('Flags a specific node type.'),
      'id' => 'node_type_flagger',
    ),
  );
}
// In this implementation we extend the node table definition by adding a new field. The most important thing to remember is the id key (under field) which marks the id of the views plugin that will be used to handle this field. Next define the handler inside the src/Plugin/views/field folder of our module:


/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\NodeTypeFlagger
 */

namespace Drupal\moduleName\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to flag the node type.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("node_type_flagger")
 */
class NodeTypeFlagger extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['node_type'] = array('default' => 'article');

    return $options;
  }

  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $types = NodeType::loadMultiple();
    $options = [];
    foreach ($types as $key => $type) {
      $options[$key] = $type->label();
    }
    $form['node_type'] = array(
      '#title' => $this->t('Which node type should be flagged?'),
      '#type' => 'select',
      '#default_value' => $this->options['node_type'],
      '#options' => $options,
    );

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    $node = $this->getEntity($values);
    if ($node->bundle() == $this->options['node_type']) {
      return $this->t('Hey, I\'m of the type: @type', array('@type' => $this->options['node_type']));
    }
    else {
      return $this->t('Hey, I\'m something else.');
    }
  }
}
