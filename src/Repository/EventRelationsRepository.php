<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Repository\EventRelationsRepository.
 */

namespace Drupal\culturefeed_udb3\Repository;


use CultuurNet\UDB3\Event\ReadModel\Relations\RepositoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Class EventRelationsRepository.
 *
 * @package Drupal\culturefeed_udb3\Repository
 */
class EventRelationsRepository implements RepositoryInterface {

  /**
   * The database connection.
   *
   * @var Connection
   */
  protected $database;

  /**
   * The query factory.
   *
   * @var QueryFactory;
   */
  protected $queryFactory;

  /**
   * EventRelationsRepository constructor.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The query factory.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(
    QueryFactory $query_factory,
    Connection $database
  ) {
    $this->database = $database;
    $this->queryFactory = $query_factory;
  }

  /**
   * {@inheritdoc}
   */
  public function removeOrganizer($event_id) {
    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_event_relations')
      ->key(array('event' => $event_id))
      ->fields(
        array(
          'organizer' => NULL,
        )
      );

    $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function storeOrganizer($event_id, $organizer_id) {
    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_event_relations')
      ->key(array('event' => $event_id))
      ->fields(
        array(
          'organizer' => $organizer_id,
        )
      );

    $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function storeRelations($event_id, $place_id, $organizer_id) {
    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_event_relations')
      ->key(array('event' => $event_id))
      ->fields(
        array(
          'place' => $place_id,
          'organizer' => $organizer_id,
        )
      );

    $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getEventsLocatedAtPlace($place_id) {
    $query = $this->queryFactory->get('culturefeed_udb3_event_relations');
    $query->condition('place', $place_id);

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getEventsOrganizedByOrganizer($organizer_id) {
    $query = $this->queryFactory->get('culturefeed_udb3_event_relations');
    $query->condition('organizer', $organizer_id);

    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function removeRelations($event_id) {
    $query = $this->database->delete('culturefeed_udb3_event_relations')
      ->condition('event', $event_id);

    return $query->execute();
  }

}
