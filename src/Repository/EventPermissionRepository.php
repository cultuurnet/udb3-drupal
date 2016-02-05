<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Repository\EventPermissionRepository.
 */

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionQueryInterface;
use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionRepositoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\Query\QueryFactory;
use ValueObjects\String\String;

/**
 * Repository for the udb3 index.
 */
class EventPermissionRepository implements PermissionRepositoryInterface, PermissionQueryInterface {

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
   * EventPermissionRepository constructor.
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
  public function markOfferEditableByUser(String $event_id, String $uit_id) {
    $query = $this->database->merge('culturefeed_udb3_event_permission')
      ->key(array('id' => $uit_id->toNative()))
      ->fields(array('event_id' => $event_id->toNative()));
    $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableOffers(String $uit_id) {
    $query = $this->queryFactory->get('culturefeed_udb3_event_permission');
    $query->condition('user_id', $uit_id->toNative());
    $result = $query->execute();

    $events = array();
    foreach ($result as $item) {
      $events[] = new String($item['event_id']);
    }
    return $events;
  }

}
