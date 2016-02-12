<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Repository\PlacePermissionRepository.
 */

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionQueryInterface;
use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionRepositoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use ValueObjects\String\String;

/**
 * Repository for the udb3 index.
 */
class PlacePermissionRepository implements PermissionRepositoryInterface, PermissionQueryInterface {

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
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * PlacePermissionRepository constructor.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The query factory.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityManagerInterface $manager
   *   The entity manager.
   */
  public function __construct(
    QueryFactory $query_factory,
    Connection $database,
    EntityManagerInterface $manager
  ) {
    $this->database = $database;
    $this->queryFactory = $query_factory;
    $this->storage = $manager->getStorage('place_permission');
  }

  /**
   * {@inheritdoc}
   */
  public function markOfferEditableByUser(String $event_id, String $uit_id) {
    $query = $this->database->merge('culturefeed_udb3_place_permission')
      ->key(array('user_id' => $uit_id->toNative()))
      ->fields(array('place_id' => $event_id->toNative()));
    $query->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableOffers(String $uit_id) {
    $query = $this->queryFactory->get('place_permission');
    $query->condition('user_id', $uit_id->toNative());
    $result = $query->execute();

    $events = array();
    foreach ($result as $item) {
      $permission = $this->storage->load($item);
      $place_id = $permission->get('place_id')->value;
      $events[] = new String($place_id);
    }
    return $events;
  }

}
