<?php

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Place\ReadModel\Relations\RepositoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Repository for place relations.
 */
class PlaceRelationsRepository implements RepositoryInterface{

  /**
   * The query factory.
   *
   * @var QueryFactory;
   */
  protected $queryFactory;

  /**
   * The database connection.
   *
   * @var Connection
   */
  protected $database;

  /**
   * @param QueryFactory $query_factory
   * @param Connection $database
   */
  public function __construct(
    QueryFactory $query_factory,
    Connection $database
  ) {
    $this->database = $database;
    $this->queryFactory = $query_factory;
  }

  /**
   * Store the relations.
   */
  public function storeRelations($placeId, $organizerId) {
    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_place_relations')
      ->key(array('place' => $placeId))
      ->fields(
        array(
          'organizer' => $organizerId
        )
      );

    $query->execute();
  }

  /**
   * Get all places by organizer.
   */
  public function getPlacesOrganizedByOrganizer($organizerId) {
    $query = $this->queryFactory->get('place_relations');
    $query->condition('organizer', $organizerId);

    return $query->execute();
  }

  public function removeRelations($placeId) {
    $query = $this->database->delete('culturefeed_udb3_place_relations')
      ->condition('place', $placeId);

    return $query->execute();
  }
}
