<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Entity\OrganizerIndex.
 */

namespace Drupal\culturefeed_udb3\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @ContentEntityType(
 *  id = "udb3_index",
 *  label = @Translation("Culturefeed udb3 index"),
 *  base_table = "culturefeed_udb3_index",
 *  entity_keys = {
 *    "id" = "id",
 *  },
 *  fieldable = FALSE,
 * )
 * @package Drupal\culturefeed_udb3\Entity
 */
class Udb3Index extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('Event / Place / Organizer UUID'))
      ->setDescription(t('Event / Place /  UUID.'));

    $fields['type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Type (place / event / organizer)'))
      ->setSetting('max_length', 128)
      ->setDescription(t('Type.'));

    $fields['uid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('Users culturefeed UUID'))
      ->setDescription(t('Users culturefeed UUID.'));

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Item title'))
      ->setSetting('max_length', 128)
      ->setDescription(t('Title.'));

    $fields['zip'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Location zip'))
      ->setSetting('max_length', 128)
      ->setDescription(t('Location zip.'));

    $fields['created_on'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Created on'))
      ->setSetting('max_length', 128)
      ->setDescription(t('Creation date.'));

    return $fields;
  }
}
