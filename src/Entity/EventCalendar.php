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
 *   id = "event_calendar",
 *   label = @Translation("UDB3 event calendar read model"),
 *   base_table = "culturefeed_udb3_event_calendar",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 *   fieldable = FALSE,
 * )
 */
class EventCalendar extends DocumentRepositoryEntity {
}
