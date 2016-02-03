<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\StorageSchema\DomainMessageStorageSchema.
 */

namespace Drupal\culturefeed_udb3\StorageSchema;

use Drupal\Core\Entity\ContentEntityTypeInterface;

/**
 * Defines the domain message schema handler.
 */
class DomainMessageStorageSchema extends Utf8StorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {

    $schema = parent::getEntitySchema($entity_type, $reset);

    $schema[$entity_type->getBaseTable()]['unique keys'] += array(
      'id' => array('uuid', 'playhead'),
    );

    return $schema;
  }

}
