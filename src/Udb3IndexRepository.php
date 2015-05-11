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
  public function updateIndex($id, $type, $userId, $title, $zip, $creationDate = null) {

    $fields_to_insert = array(
      'type' => $type,
      'uid' => $userId,
      'title' => $title,
      'zip' => $zip,
    );

    if (!empty($creationDate)) {
      $fields_to_insert['created_on'] = $creationDate->getTimestamp();
    }

    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_index')
      ->key(array('id' => $id))
      ->fields($fields_to_insert);

    $query->execute();
  }

  /**
   * Delete the index for a place/event.
   * @param type $id
   */
  public function deleteIndex($id) {
    $query = $this->database->delete('culturefeed_udb3_index')
      ->condition('id', $id);

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
