<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Repository\EventPermissionRepository.
 */

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionQueryInterface;
use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionRepositoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\IntegrityConstraintViolationException;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use ValueObjects\String\String;

/**
 * Repository for the udb3 index.
 */
class EventPermissionRepository implements PermissionRepositoryInterface, PermissionQueryInterface {

  /**
   * The database connection.
   *
   * @var Connection
   */
  protected $database;

  /**
   * The query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $queryFactory;

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * EventPermissionRepository constructor.
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
    $this->storage = $manager->getStorage('event_permission');
  }

  /**
   * {@inheritdoc}
   */
  public function markOfferEditableByUser(String $event_id, String $uit_id) {

    $permission = $this->storage->create(array(
      'user_id' => $uit_id->toNative(),
      'event_id' => $event_id->toNative(),
    ));

    try {
      $this->storage->save($permission);
    }
    catch (IntegrityConstraintViolationException $e) {
      // Intentionally catching database exception occurring when the
      // permission record is already in place.
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getEditableOffers(String $uit_id) {
    $query = $this->queryFactory->get('event_permission');
    $query->condition('user_id', $uit_id->toNative());
    $result = $query->execute();

    $events = array();
    foreach ($result as $item) {
      $permission = $this->storage->load($item);
      $event_id = $permission->get('event_id')->value;
      $events[] = new String($event_id);
    }
    return $events;
  }

}
