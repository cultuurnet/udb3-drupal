<?php

namespace Drupal\culturefeed_udb3\Entity;

/**
 * Defines the culturefeed udb3 organizer store.
 *
 * @ContentEntityType(
 *   id = "organizer_store",
 *   label = @Translation("Culturefeed udb3 organizer store"),
 *   base_table = "culturefeed_udb3_organizer_store",
 *   handlers = {
 *     "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\DomainMessageStorageSchema",
 *   },
 *   entity_keys = {
 *     "id" = "dmid",
 *   },
 *   fieldable = FALSE,
 * )
 */
class OrganizerStoreEntity extends DomainMessageEntity {
}
