<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Utf8StorageSchema.
 */

namespace Drupal\culturefeed_udb3;

use Drupal\Core\Entity\Sql\SqlContentEntityStorageSchema;
use Drupal\Core\Entity\ContentEntityTypeInterface;

/**
 * Defines the domain message schema handler.
 */
class Utf8StorageSchema extends SqlContentEntityStorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {

    $schema = parent::getEntitySchema($entity_type, $reset);
    $schema[$entity_type->getBaseTable()]['mysql_character_set'] = 'utf8';
    return $schema;

  }

}
