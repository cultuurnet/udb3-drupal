<?php

namespace Drupal\culturefeed_udb3\Entity;

/**
 * Defines the culturefeed udb3 place store.
 *
 * @ContentEntityType(
 *   id = "place_store",
 *   label = @Translation("Culturefeed udb3 place store"),
 *   base_table = "culturefeed_udb3_place_store",
 *   handlers = {
 *     "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\DomainMessageStorageSchema",
 *   },
 *   entity_keys = {
 *     "id" = "dmid",
 *   },
 *   fieldable = FALSE,
 * )
 */
class PlaceStoreEntity extends DomainMessageEntity {
}
