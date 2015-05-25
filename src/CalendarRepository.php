<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\EntityLdRepository.
 */

namespace Drupal\culturefeed_udb3;

use CultuurNet\UDB3\Event\ReadModel\Calendar\CalendarRepositoryInterface;
use CultureFeed_Cdb_Data_Calendar as Calendar;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Class EntityLdRepository.
 *
 * @package Drupal\culturefeed_udb3
 */
class CalendarRepository implements CalendarRepositoryInterface {

  /**
   * The entity Manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface;
   */
  protected $entityManager;

  /**
   * Constructs the entity calendar repository.
   *
   * @param EntityManagerInterface $entity_manager
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager) {

    $this->entityManager = $entity_manager->getStorage('event_calendar');

  }

  /**
   * {@inheritdoc}
   */
  public function get($id) {

    $entity = $this->entityManager->load($id);
    if (isset($entity->body->value) && $entity->body->value) {
      return unserialize($entity->body->value);
    }

  }

  /**
   * {@inheritdoc}
   */
  public function save($id, Calendar $calendar) {

    $entity = $this->entityManager->load($id);
    if ($entity) {
      $entity->body->value = serialize($calendar);
      $entity->save();
    }
    else {
      $entity = $this->entityManager->create(array(
        'id' => $id,
        'body' => serialize($calendar),
      ));
    }
    $entity->save();

  }

}
