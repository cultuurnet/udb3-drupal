<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Entity\DomainMessageEntity.
 */

namespace Drupal\culturefeed_udb3\Entity;

use Drupal\culturefeed_udb3\DomainMessageEntity;

/**
 * Defines the culturefeed udb3 used labels memory event store.
 *
 * @ContentEntityType(
 *   id = "used_labels_memory_event_store",
 *   label = @Translation("Culturefeed udb3 used labels memory event store"),
 *   base_table = "culturefeed_udb3_used_labels_memory_event_store",
 *   handlers = {
 *     "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\DomainMessageStorageSchema",
 *   },
 *   entity_keys = {
 *     "id" = "dmid",
 *   },
 *   fieldable = FALSE,
 * )
 */
class UsedLabelsMemoryEventStoreEntity extends DomainMessageEntity {
}
