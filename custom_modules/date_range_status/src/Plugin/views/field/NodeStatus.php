<?php

namespace Drupal\date_range_status\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * A handler to provide a field that displays the total number of learners in a pathway.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("node_status")
 */
class NodeStatus extends FieldPluginBase
{
  /**
   * {@inheritdoc}
   */
  public function query()
  {
    // Do nothing -- to override the parent query.
  }
   /**
   * Define the available options
   * @return array
   */
  protected function defineOptions()
  {
    $options = parent::defineOptions();
    $options['field_date'] = ['default' => ''];

    return $options;
  }


  /**
   * Provide the options form.
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state)
  {
    $form['field_date'] = array(
      '#title' => $this->t('machine name of the date range field'),
      '#type' => 'textfield',
      '#default_value' => $this->options['field_date'],
    );

    parent::buildOptionsForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values)
  {
    $field_date = $this->options['field_date'];
    $node = $this->getEntity($values);
    $node_status = '';
    $node_status_class = '';
    $current_date = \Drupal::time()->getCurrentTime();
    $start_date = $node->get($field_date)->value ? strtotime($node->get($field_date)->value) : 0;
    $expire_date = $node->get($field_date)->end_value ? strtotime($node->get($field_date)->end_value) : 0;

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
    return $node_status;
  }
}
