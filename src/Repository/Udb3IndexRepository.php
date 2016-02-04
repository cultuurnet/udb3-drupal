<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Repository\Udb3IndexRepository.
 */

namespace Drupal\culturefeed_udb3\Repository;

use DateTimeInterface;
use CultuurNet\UDB3\ReadModel\Index\EntityType;
use CultuurNet\UDB3\ReadModel\Index\RepositoryInterface;
use CultuurNet\UDB3\Place\ReadModel\Lookup\PlaceLookupServiceInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Repository for the udb3 index.
 */
class Udb3IndexRepository implements RepositoryInterface, PlaceLookupServiceInterface  {

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
   * Udb3IndexRepository constructor.
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
  public function updateIndex($id, EntityType $entity_type, $user_id, $name, $postal_code, DateTimeInterface $created = NULL) {

    $fields_to_insert = array(
      'type' => $entity_type->toNative(),
      'uid' => $user_id,
      'title' => $name,
      'zip' => $postal_code,
    );

    if (!empty($created)) {
      $fields_to_insert['created_on'] = $created->getTimestamp();
    }

    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_index')
      ->key(array('id' => $id))
      ->fields($fields_to_insert);

    $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function deleteIndex($id, EntityType $entity_type) {
    $query = $this->database->delete('culturefeed_udb3_index')
      ->condition('id', $id)
      ->condition('type', $entity_type->toNative());

    return $query->execute();

  }

  /**
   * {@inheritdoc}
   */
  public function findPlacesByPostalCode($postal_code) {
    $query = $this->queryFactory->get('udb3_index');
    $query->condition('zip', $postal_code);
    $query->condition('type', EntityType::PLACE()->toNative());

    return $query->execute();
  }

  /**
   * Search organizers that contain given title.
   */
  public function getOrganizersByTitle($title, $limit = 10) {

    $query = $this->queryFactory->get('udb3_index');
    $query->condition('title', '%' . db_like($title) . '%', 'LIKE');
    $query->condition('type', 'organizer');
    $query->range(0, $limit);

    return $query->execute();
  }

  /**
   * Search organizers that contain given title and the zip code.
   */
  public function getOrganizersByTitleAndZip($title, $zip, $limit = 10) {

    $query = $this->queryFactory->get('udb3_index');
    $query->condition('title', '%' . db_like($title) . '%', 'LIKE');
    $query->condition('type', 'organizer');
    $query->condition('zip', $zip);
    $query->range(0, $limit);

    return $query->execute();
  }

}
