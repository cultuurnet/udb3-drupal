<?php

namespace Drupal\culturefeed_udb3\Entity;

use Drupal\culturefeed_udb3\DomainMessageEntity;

/**
 * Defines the culturefeed udb3 variation store.
 *
 * @ContentEntityType(
 *   id = "variation_store",
 *   label = @Translation("Culturefeed udb3 variation store"),
 *   base_table = "culturefeed_udb3_variation_store",
 *   handlers = {
 *     "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\DomainMessageStorageSchema",
 *   },
 *   entity_keys = {
 *     "id" = "dmid",
 *   },
 *   fieldable = FALSE,
 * )
 */
class VariationStoreEntity extends DomainMessageEntity {
}
