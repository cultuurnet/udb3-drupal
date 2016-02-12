<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Entity\EventPermissionEntity.
 */

namespace Drupal\culturefeed_udb3\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the culturefeed udb3 place store.
 *
 * @ContentEntityType(
 *   id = "event_permission",
 *   label = @Translation("Culturefeed udb3 event permission"),
 *   base_table = "culturefeed_udb3_event_permission",
 *   handlers = {
 *     "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\EventPermissionStorageSchema",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   fieldable = FALSE,
 * )
 */
class EventPermissionEntity extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Add an id for entity purposes.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Event permission ID'))
      ->setDescription(t('The event permission ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User id'))
      ->setReadOnly(TRUE)
      ->setDescription(t('User id.'));

    $fields['event_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Event id'))
      ->setReadOnly(TRUE)
      ->setDescription(t('Event id.'));

    return $fields;
  }

}
