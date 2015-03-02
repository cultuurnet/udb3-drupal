<?php
/**
 * @file
 * Contains Drupal\culturefeed_udb3\OrganizerIndexRepository.
 */

namespace Drupal\culturefeed_udb3;

use CultuurNet\UDB3\ReadModel\Index\RepositoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Repository for the udb3 index.
 */
class Udb3IndexRepository implements RepositoryInterface {

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
   * Update the index.
   */
  public function updateIndex($id, $type, $userId, $name, $zip) {
    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_udb3_index')
      ->key(array('id' => $id))
      ->fields(
        array(
          'type' => $type,
          'uid' => $userId,
          'name' => $name,
          'zip' => $zip,
        )
      );

    $query->execute();
  }

}
