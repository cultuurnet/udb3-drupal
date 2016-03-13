<?php

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Place\ReadModel\Relations\RepositoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Repository for place relations.
 */
class PlaceRelationsRepository implements RepositoryInterface {

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
   * PlaceRelationsRepository constructor.
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
  public function storeRelations($place_id, $organizer_id) {
    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_place_relations')
      ->key(array('place' => $place_id))
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
  public function getPlacesOrganizedByOrganizer($organizer_id) {
    $query = $this->queryFactory->get('place_relations');
    $query->condition('organizer', $organizer_id);
    return $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function removeRelations($place_id) {
    $query = $this->database->delete('culturefeed_udb3_place_relations')
      ->condition('place', $place_id);

    return $query->execute();
  }

}
