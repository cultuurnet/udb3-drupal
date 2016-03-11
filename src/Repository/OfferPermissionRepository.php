<?php

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionQueryInterface;
use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionRepositoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\IntegrityConstraintViolationException;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use ValueObjects\String\String;

/**
 * Class OfferPermissionRepository.
 *
 * @package Drupal\culturefeed_udb3\Repository
 */
class OfferPermissionRepository implements PermissionRepositoryInterface, PermissionQueryInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
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
   * The offer type.
   *
   * @var string
   */
  protected $offerType;

  /**
   * OfferPermissionRepository constructor.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The query factory.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityManagerInterface $manager
   *   The entity manager.
   * @param string $offer_type
   *   The offer type. Eg. "place", "event"
   */
  public function __construct(
    QueryFactory $query_factory,
    Connection $database,
    EntityManagerInterface $manager,
    $offer_type
  ) {
    $this->database = $database;
    $this->queryFactory = $query_factory;
    $this->storage = $manager->getStorage($offer_type . '_permission');
    $this->offerType = $offer_type;
  }

  /**
   * {@inheritdoc}
   */
  public function markOfferEditableByUser(String $offer_id, String $uit_id) {

    $permission = $this->storage->create(array(
      'user_id' => $uit_id->toNative(),
      $this->offerType . '_id' => $offer_id->toNative(),
    ));

    try {
      $this->storage->save($permission);
    }
    catch (IntegrityConstraintViolationException $e) {
      // Intentionally catching database exception occurring when the
      // permission record is already in place, which means that the
      // offer is already editable by the user.
    }

  }

  /**
   * {@inheritdoc}
   */
  public function getEditableOffers(String $uit_id) {
    $query = $this->queryFactory->get($this->offerType . '_permission');
    $query->condition('user_id', $uit_id->toNative());
    $result = $query->execute();

    $offers = array();
    foreach ($result as $item) {
      $permission = $this->storage->load($item);
      $offer_id = $permission->get($this->offerType . '_id')->value;
      $offers[] = new String($offer_id);
    }
    return $offers;
  }

}
