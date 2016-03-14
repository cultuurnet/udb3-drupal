<?php

/**
 * @file
 * Contains culturefeed_udb3.drush.inc.
 */

/**
 * Implements hook_drush_command().
 */
function culturefeed_udb3_drush_command() {
  $items = array();

  $items['udb3-worker'] = array(
    'description' => 'The culturefeed udb3 worker.',
    'options' => array(),
    'arguments' => array(),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_SITE,
    'core' => array('8+'),
    'aliases' => array('udb3w'),
  );

  $items['udb3-delete-data'] = array(
    'description' => 'Delete all culturefeed udb3 data: document store and repository.',
    'options' => array(),
    'arguments' => array(),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_SITE,
    'core' => array('8+'),
    'aliases' => array('udb3d'),
  );

  return $items;
}

/**
 * Deletes all culturefeed udb3 data.
 */
function drush_culturefeed_udb3_udb3_delete_data() {

  // Delete the domain message implementations.
  $entities = array(
    'event_permission',
    'event_relations',
    'event_store',
    'media_object_store',
    'organizer_store',
    'place_permission',
    'place_relations',
    'place_store',
    'udb3_index',
    'used_labels_memory_event_store',
    'variation',
    'variation_store',
  );

  foreach ($entities as $entity_type) {
    $results = entity_load_multiple($entity_type);
    $entity_ids = array_keys($results);
    entity_delete_multiple($entity_type, $entity_ids);
  }

  // Clear the caches.
  $container = \Drupal::getContainer();
  foreach ($container->getParameter('cache_bins') as $service_id => $bin) {

    $bin = $container->get($service_id);
    $class = get_class($bin);

    if ($class == 'Drupal\culturefeed_udb3\Cache\DatabaseBackend') {
      $bin->realDeleteAll();
    }

  }
}

/**
 * Starts the culturefeed udb3 worker.
 */
function drush_culturefeed_udb3_udb3_worker() {

  $path = drupal_get_path('module', 'culturefeed_udb3');
  $command = $path . '/culturefeed_udb3.worker.php';
  $command .= " --drupal_root=" . DRUPAL_ROOT;
  $command .= " --uri=" . $GLOBALS['base_url'];
  drush_shell_proc_open($command);
}
