<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\StorageSchema\EventPermissionStorageSchema.
 */

namespace Drupal\culturefeed_udb3\StorageSchema;

use Drupal\Core\Entity\ContentEntityTypeInterface;

/**
 * Defines the event permission schema handler.
 */
class EventPermissionStorageSchema extends Utf8StorageSchema {

  /**
   * {@inheritdoc}
   */
  protected function getEntitySchema(ContentEntityTypeInterface $entity_type, $reset = FALSE) {

    $schema = parent::getEntitySchema($entity_type, $reset);

    $schema[$entity_type->getBaseTable()]['unique keys'] += array(
      'id' => array('event_id', 'user_id'),
    );

    return $schema;
  }

}
