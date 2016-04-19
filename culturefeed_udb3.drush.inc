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

  $items['udb3-replay'] = array(
    'description' => 'The culturefeed udb3 replay command.',
    'options' => array(
      'cache' => 'Alternative cache factory method to use, specify the service suffix, for example "redis".',
      'subscriber' => 'Subscribers to register with the event bus. If not specified, all subscribers will be registered.',
      'start-id' => 'The id of the row to start the replay from.',
      'disable-publishing' => 'Disable publishing to the event bus.',
    ),
    'arguments' => array(
      'store' => 'The event store table',
    ),
    'bootstrap' => DRUSH_BOOTSTRAP_DRUPAL_SITE,
    'core' => array('8+'),
    'aliases' => array('udb3r'),
  );

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
 * Replay an udb3 event store.
 *
 * @param string $store
 *   The event store table.
 */
function drush_culturefeed_udb3_udb3_replay($store) {

  // Event bus.
  /* @var \CultuurNet\UDB3\SimpleEventBus $event_bus */
  $event_bus = Drupal::service('culturefeed_udb3.event_bus');

  // Subscribers.
  // @todo The drupal subscribers are already in here.
  $subscribers = drush_get_option('subscriber');
  if (!empty($subscribers)) {
    drush_log(
      'Registering the following subscribers with the event bus: ' . $subscribers,
      \Drush\Log\LogLevel::OK
    );
    foreach ($subscribers as $subscriber) {
      $event_bus->subscribe($subscriber);
    }
  }

  // Store.
  $valid_stores = [
    'events' => 'culturefeed_udb3_event_store',
    'places' => 'culturefeed_udb3_place_store',
    'organizers' => 'culturefeed_udb3_organizer_store',
    'variations' => 'culturefeed_udb3_variation_store',
  ];

  if (!in_array($store, array_keys($valid_stores))) {
    drush_log(
      'Invalid store "' . $store . '"", use one of: ' . implode(', ', array_keys($valid_stores)),
      \Drush\Log\LogLevel::ERROR
    );
    exit();
  }
  $store = $valid_stores[$store];

  // Start_id.
  $start_id = drush_get_option('start-id');

  // DBAL connection.
  /* @var \Doctrine\DBAL\Connection $dbal */
  $dbal = Drupal::service('culturefeed_udb3.dbal_connection');

  // Payload serializer.
  /* @var \CultuurNet\UDB3\EventSourcing\PayloadManipulatingSerializer $payload_serializer */
  $payload_serializer = Drupal::service('culturefeed_udb3.eventstore_payload_serializer');

  // Stream.
  $stream = new \CultuurNet\UDB3\EventSourcing\DBAL\EventStream(
    $dbal,
    $payload_serializer,
    new \Broadway\Serializer\SimpleInterfaceSerializer(),
    $store,
    $start_id !== null ? $start_id : 0
  );

  /* @var \Broadway\Domain\DomainEventStream $eventStream */
  foreach ($stream() as $event_stream) {
    /* @var \Broadway\Domain\DomainMessage $message */
    foreach ($event_stream->getIterator() as $message) {
      drush_log(
        $stream->getPreviousId() . '. ' .
        $message->getRecordedOn()->toString() . ' ' .
        $message->getType() . ' (' . $message->getId() . ')',
        \Drush\Log\LogLevel::OK
      );
    }

    if (drush_get_option('disable-publishing', TRUE)) {
      $event_bus->publish($event_stream);
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
