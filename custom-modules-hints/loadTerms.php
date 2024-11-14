<?php

// Load all terms of a vocabulary
use Drupal\taxonomy\Entity\Term;

function getTaxonomyBuild(){

    $term_data = [];

    $default_langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $vid = 'XX';

    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

    foreach($terms as $term) {

      $taxonomy_term = Term::load($term->tid);

      $taxonomy_term_trans = \Drupal::service('entity.repository')->getTranslationFromContext($taxonomy_term, $default_langcode);

      $categories[$term->tid] = $taxonomy_term_trans->getName();

      $term_data[] = array(

        'id' => $term->tid,

        'name' => $categories[$term->tid],

      );

    }

    return $term_data;

}