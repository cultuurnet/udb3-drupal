<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Entity\EventHistory.
 */

namespace Drupal\culturefeed_udb3\Entity;

use Drupal\Core\Entity\Annotation\ContentEntityType;
use Drupal\culturefeed_udb3\DocumentRepositoryEntity;
use Drupal\Core\Annotation\Translation;

/**
 * Defines the culturefeed udb3 event document repository entity.
 *
 * @ContentEntityType(
 *   id = "event_history",
 *   label = @Translation("UDB3 event history read model"),
 *   base_table = "culturefeed_udb3_event_history",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   fieldable = FALSE,
 * )
 */
class EventHistory extends DocumentRepositoryEntity {
}
