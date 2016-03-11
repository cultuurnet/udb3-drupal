<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Entity\MediaObjectStoreEntity.
 */

namespace Drupal\culturefeed_udb3\Entity;

use Drupal\culturefeed_udb3\DomainMessageEntity;

/**
 * Defines the culturefeed udb3 media object store.
 *
 * @ContentEntityType(
 *   id = "media_object_store",
 *   label = @Translation("Culturefeed udb3 media object store"),
 *   base_table = "culturefeed_udb3_media_object_store",
 *   handlers = {
 *     "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\DomainMessageStorageSchema",
 *   },
 *   entity_keys = {
 *     "id" = "dmid",
 *   },
 *   fieldable = FALSE,
 * )
 */
class MediaObjectStoreEntity extends DomainMessageEntity {
}
