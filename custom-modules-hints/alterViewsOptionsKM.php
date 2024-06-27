// in hook form_alter just check $form['#id'] of the view

if($form['#id'] == 'views-exposed-form-taxonomy-term-page-1'){

// to get an array of choices to make entity query on it.
   $options = $form['type']['#options'];

// to get term id from url
   $path = \Drupal::service('path.current')->getPath();
   $path_array = explode('/', $path);
   $term_id = $path_array[3];

// loop on options array that contains options in dropdown
   foreach($options as $key => $value){
     if($key == 'All'){
       $count = \Drupal::entityQuery('node')->condition('field_main_category', $term_id)
       ->count()
       ->execute();

// embed the count to option after getting it
       $form['type']['#options'][$key] = t('All').' ('.$count.')';
     }
     else{
       $count = \Drupal::entityQuery('node')->condition('type', $key)
                    ->condition('field_main_category', $term_id)             
                    ->count()
                    ->execute(); 
       $form['type']['#options'][$key] = $value.' ('.$count.')';
     }
   } 
 } 