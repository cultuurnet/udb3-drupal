<?php

namespace Drupal\culturefeed_udb3\Entity;

/**
 * Defines the culturefeed udb3 event store.
 *
 * @ContentEntityType(
 *   id = "event_store",
 *   label = @Translation("Culturefeed udb3 event store"),
 *   base_table = "culturefeed_udb3_event_store",
 *   handlers = {
 *     "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\DomainMessageStorageSchema",
 *   },
 *   entity_keys = {
 *     "id" = "dmid",
 *   },
 *   fieldable = FALSE,
 * )
 */
class EventStoreEntity extends DomainMessageEntity {
}
