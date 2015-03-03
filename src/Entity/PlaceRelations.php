<?php
/**
 * @file
 */

namespace Drupal\culturefeed_udb3\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @ContentEntityType(
 *  id = "place_relations",
 *  label = @Translation("Culturefeed udb3 place relations"),
 *  base_table = "culturefeed_udb3_place_relations",
 *  entity_keys = {
 *    "id" = "place",
 *  },
 *  fieldable = FALSE,
 * )
 * @package Drupal\culturefeed_udb3\Entity
 */
class PlaceRelations extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['place'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Place UUID'))
      ->setDescription(t('Place UUID.'));

    $fields['organizer'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Organizer UUID'))
      ->setDescription(t('Organizer UUID.'));

    return $fields;
  }
}
