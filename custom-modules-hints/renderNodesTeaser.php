// Render Nodes in display mode 'teaser' with pager in entity Query

// this an example on Book content type 

public function data(){

  $list = ['nodes' => []];

  $query = \Drupal::entityQuery('node')

      ->condition('type', 'book')

      ->condition('status', 1)

      ->sort('created', 'DESC')

      ->pager(16);

  $nids = $query->execute();

  $entity_type_manager = \Drupal::entityTypeManager();
  $node_view_builder = $entity_type_manager->getViewBuilder('node');
  $view_mode = 'teaser';

  if (!empty($nids)) {
    $nodes = $entity_type_manager->getStorage('node')->loadMultiple($nids);
    foreach ($nodes as $node) {
       $list['nodes'][$node->id()] = $node_view_builder->view($node, $view_mode);
     }
  }

  return $list;

}

// in to render this result in twig

public function build() {

    return [

      'results' => [

        '#theme' => 'base_books_page',

        '#items' => $this->data(),

      ],

      'pager' => [

        '#type' => 'pager',

      ],

    ];

  }

// to show nodes with this display mode in teaser in twig template just type

{{ items }}
