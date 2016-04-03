<?php

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Dashboard\DashboardItemLookupServiceInterface;
use CultuurNet\UDB3\Offer\IriOfferIdentifier;
use CultuurNet\UDB3\Offer\OfferIdentifierCollection;
use CultuurNet\UDB3\Offer\OfferType;
use CultuurNet\UDB3\Organizer\ReadModel\Lookup\OrganizerLookupServiceInterface;
use CultuurNet\UDB3\Place\ReadModel\Lookup\PlaceLookupServiceInterface;
use CultuurNet\UDB3\ReadModel\Index\EntityIriGeneratorFactoryInterface;
use CultuurNet\UDB3\ReadModel\Index\EntityType;
use CultuurNet\UDB3\ReadModel\Index\RepositoryInterface;
use CultuurNet\UDB3\Search\Results;
use DateTimeInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Query\Condition;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use ValueObjects\Number\Integer;
use ValueObjects\Number\Natural;
use ValueObjects\Web\Domain;

/**
 * Repository for the udb3 index.
 */
class Udb3IndexRepository implements DashboardItemLookupServiceInterface, OrganizerLookupServiceInterface, PlaceLookupServiceInterface, RepositoryInterface {

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
   * The entity iri generator factory.
   *
   * @var \CultuurNet\UDB3\ReadModel\Index\EntityIriGeneratorFactoryInterface
   */
  protected $entityIriGeneratorFactory;

  /**
   * The storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * Udb3IndexRepository constructor.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   The query factory.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $manager
   *   The entity type manager.
   * @param \CultuurNet\UDB3\ReadModel\Index\EntityIriGeneratorFactoryInterface $entity_iri_generator_factory
   *   The entity iri generator factory.
   */
  public function __construct(
    QueryFactory $query_factory,
    Connection $database,
    EntityTypeManagerInterface $manager,
    EntityIriGeneratorFactoryInterface $entity_iri_generator_factory
  ) {
    $this->database = $database;
    $this->queryFactory = $query_factory;
    $this->entityIriGeneratorFactory = $entity_iri_generator_factory;
    $this->storage = $manager->getStorage('udb3_index');
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
  public function findByUser(
    $user_id,
    Natural $limit,
    Natural $start
  ) {

    $query = $this->queryFactory->get('udb3_index');
    $query->condition('uid', $user_id);
    $or = new Condition('OR');
    $or->condition('type', 'place');
    $or->condition('type', 'event');
    $query->condition($or);

    return $this->getPagedDashboardItems($query, $start, $limit);

  }

  /**
   * {@inheritdoc}
   */
  public function findByUserForDomain(
    $user_id,
    Natural $limit,
    Natural $start,
    Domain $owning_domain
  ) {

    $query = $this->queryFactory->get('udb3_index');
    $query->condition('uid', $user_id);
    $query->condition('owning_domain', $owning_domain->toNative());
    $or = $query->orConditionGroup();
    $or->condition('type', 'place');
    $or->condition('type', 'event');
    $query->condition($or);

    return $this->getPagedDashboardItems($query, $start, $limit);

  }

  /**
   * {@inheritdoc}
   */
  public function findOrganizersByPartOfTitle($title, $limit = 10) {

    $query = $this->queryFactory->get('udb3_index');
    $query->condition('title', '%' . db_like($title) . '%', 'LIKE');
    $query->condition('type', 'organizer');
    $query->range(0, $limit);

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
   * Get the paged dashboard items.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   *   The query.
   * @param \ValueObjects\Number\Natural $start
   *   The start.
   * @param \ValueObjects\Number\Natural $limit
   *   The limit.
   *
   * @return \CultuurNet\UDB3\Search\Results
   *   The results.
   */
  private function getPagedDashboardItems(
    QueryInterface $query,
    Natural $start,
    Natural $limit
  ) {

    // Results query.
    $results_query = clone($query);
    $results_query->range($start->toNative(), $limit->toNative());
    $results_query->sort('updated', 'DESC');
    $results = $results_query->execute();
    $offer_identifier_array = array_map(

      function ($id) {

        $item = $this->storage->load($id);

        $offer_identifier = new IriOfferIdentifier(
          $item->get('entity_iri')->value,
          $id,
          OfferType::fromNative(ucfirst($item->get('type')->value))
        );
        return $offer_identifier;

      },
      $results

    );
    $offer_identifier_collection = OfferIdentifierCollection::fromArray($offer_identifier_array);

    // Count query.
    $count_query = clone($query);
    $count_query->count();
    $count = new Integer($count_query->execute());

    return new Results($offer_identifier_collection, $count);

  }

  /**
   * {@inheritdoc}
   */
  public function setUpdateDate($id, DateTimeInterface $updated) {

    $fields_to_insert = array(
      'updated' => $updated->getTimestamp(),
    );

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
  public function updateIndex($id, EntityType $entity_type, $user_id, $name, $postal_code, Domain $owning_domain, DateTimeInterface $created = NULL) {

    $iriGenerator = $this->entityIriGeneratorFactory->forEntityType($entity_type);
    $iri = $iriGenerator->iri($id);

    $fields_to_insert = array(
      'type' => $entity_type->toNative(),
      'uid' => $user_id,
      'title' => $name,
      'zip' => $postal_code,
      'owning_domain' => $owning_domain->toNative(),
      'entity_iri' => $iri,
    );

    if (!empty($created)) {
      $fields_to_insert['created_on'] = $fields_to_insert['updated'] = $created->getTimestamp();
    }

    // For optimal performance we use a merge query here
    // instead of the entity API.
    $query = $this->database->merge('culturefeed_udb3_index')
      ->key(array('id' => $id))
      ->fields($fields_to_insert);

    $query->execute();
  }

}
