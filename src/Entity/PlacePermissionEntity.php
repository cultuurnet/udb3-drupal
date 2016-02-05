<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Entity\PlacePermissionEntity.
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
 *   id = "place_permission",
 *   label = @Translation("Culturefeed udb3 place permission"),
 *   base_table = "culturefeed_udb3_place_permission",
 *   handlers = {
 *     "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\PlacePermissionStorageSchema",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   fieldable = FALSE,
 * )
 */
class PlacePermissionEntity extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Add an id for entity purposes.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Place permission ID'))
      ->setDescription(t('The place permission ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('User id'))
      ->setReadOnly(TRUE)
      ->setDescription(t('User id.'));

    $fields['place_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Place id'))
      ->setReadOnly(TRUE)
      ->setDescription(t('Place id.'));

    return $fields;
  }

}
