<?php

namespace Drupal\date_range_status\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\StringFilter;

/**
 * Filter by start and end date.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("node_status_filter")
 */
class NodeStatusFilter extends StringFilter {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['node_status'] = ['default' => 'All'];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $status = ["all" => t('All'), "open" => t('Open'), "closed" => t('Closed'), "later" => t('Later')];
    $form['node_status'] = [
      '#type' => 'select',
      '#title' => $this->t('Node Status'),
      '#description' => $this->t('list of options to define node status.'),
      '#options' => $status,
      '#default_value' => $this->$options['node_status'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(&$form, FormStateInterface $form_state) {
    parent::validateOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();

    // $current_date = \Drupal::time()->getCurrentTime();
    // $start_date = $node->get($field_date)->value ? strtotime($node->get($field_date)->value) : 0;
    // $expire_date = $node->get($field_date)->end_value ? strtotime($node->get($field_date)->end_value) : 0;
    /** @var \Drupal\views\Plugin\views\query\Sql $query */
    $query = $this->query;
    if ($options['node_status'] === 'all') {
        $query->condition($field_name, $value, $operator);
    }
    else if ($options['node_status'] === 'open') {
        $query->condition($field_name, $value, $operator);
    }
    else if ($options['node_status'] === 'closed') {
        $query->condition($field_name, $value, $operator);
    }
    else {
        $query->condition($field_name, $value, $operator);
    }
    // $query->addWhereExpression(0, "EXTRACT(YEAR FROM FROM_UNIXTIME(node_field_data.created)) = :year", [':year' => $this->value[0]]);
    
    // /** @var \Drupal\views\Plugin\views\query\Sql $query */
    // $query = $this->query;
    // $table = array_key_first($query->tables);

    // $first_day_last_month = strtotime($this->options['start_date']);
    // $query->addWhere($this->options['group'], $table . '.created', $first_day_last_month, '>=');

    // $first_day_this_month = strtotime($this->options['end_date']);
    // $query->addWhere($this->options['group'], $table . '.created', $first_day_this_month, '<=');
    // dd("asd");
  }

}