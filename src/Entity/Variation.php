<?php

namespace Drupal\culturefeed_udb3\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Class Variation.
 *
 * @ContentEntityType(
 *  id = "variation",
 *  label = @Translation("Culturefeed udb3 variation"),
 *  base_table = "culturefeed_udb3_variation",
 *  handlers = {
 *    "storage_schema" = "Drupal\culturefeed_udb3\StorageSchema\Utf8StorageSchema",
 *  },
 *  entity_keys = {
 *    "id" = "id",
 *  },
 *  fieldable = FALSE,
 * )
 *
 * @package Drupal\culturefeed_udb3\Entity
 */
class Variation extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Variation UUID'))
      ->setDescription(t('Variation UUID.'));

    $fields['event'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Event UUID'))
      ->setDescription(t('Event UUID.'));

    $fields['owner'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Owner ID'))
      ->setDescription(t('Owner ID.'));

    $fields['purpose'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Purpose'))
      ->setDescription(t('Purpose.'));

    $fields['inserted'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Inserted'))
      ->setDescription(t('Inserted.'));

    return $fields;
  }

}
