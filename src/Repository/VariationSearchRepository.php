<?php

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Variations\Model\Properties\Id;
use CultuurNet\UDB3\Variations\Model\Properties\OwnerId;
use CultuurNet\UDB3\Variations\Model\Properties\Purpose;
use CultuurNet\UDB3\Variations\Model\Properties\Url;
use CultuurNet\UDB3\Variations\ReadModel\Search\Criteria;
use CultuurNet\UDB3\Variations\ReadModel\Search\Doctrine\ExpressionFactory;
use CultuurNet\UDB3\Variations\ReadModel\Search\RepositoryInterface;
use Doctrine\DBAL\Connection;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Class VariationSearchRepository.
 *
 * @package Drupal\culturefeed_udb3\Repository
 */
class VariationSearchRepository implements RepositoryInterface {

  /**
   * The dbal connection.
   *
   * @var \Doctrine\DBAL\Connection
   */
  protected $connection;

  /**
   * The expression factory.
   *
   * @var \CultuurNet\UDB3\Variations\ReadModel\Search\Doctrine\ExpressionFactory
   */
  protected $expressionFactory;

  /**
   * The entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The table name.
   *
   * @var string
   */
  protected $tableName;

  /**
   * VariationSearchRepository constructor.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $manager
   *   The entity manager.
   * @param \Doctrine\DBAL\Connection $connection
   *   The dbal connection.
   * @param \CultuurNet\UDB3\Variations\ReadModel\Search\Doctrine\ExpressionFactory $expression_factory
   *   The expression factory.
   */
  public function __construct(
    EntityManagerInterface $manager,
    Connection $connection,
    ExpressionFactory $expression_factory
  ) {
    $this->storage = $manager->getStorage('variation');
    $this->connection = $connection;
    $this->expressionFactory = $expression_factory;
    $this->tableName = 'culturefeed_udb3_variation';
  }

  /**
   * {@inheritdoc}
   */
  public function save(
    Id $variation_id,
    Url $origin_url,
    OwnerId $owner_id,
    Purpose $purpose
  ) {

    $variation = $this->storage->create(array(
      'id' => (string) $variation_id,
      'owner' => (string) $owner_id,
      'purpose' => (string) $purpose,
      'inserted' => time(),
      'origin_url' => (string) $origin_url,
    ));
    $variation->save();

  }

  /**
   * {@inheritdoc}
   */
  public function countOfferVariations(
    Criteria $criteria
  ) {

    $q = $this->connection->createQueryBuilder();
    $q
      ->select('COUNT(id) as total')
      ->from($this->tableName);

    $conditions = $this->expressionFactory->createExpressionFromCriteria(
      $q->expr(),
      $criteria
    );

    if ($conditions) {
      $q->where($conditions);
    }

    return intval($q->execute()->fetchColumn(0));

  }

  /**
   * {@inheritdoc}
   */
  public function getOfferVariations(
    Criteria $criteria,
    $limit = 30,
    $page = 0
  ) {

    $offset = $limit * $page;
    $q = $this->connection->createQueryBuilder();
    $q
      ->select('id')
      ->from($this->tableName)
      ->orderBy('inserted')
      ->setFirstResult($offset)
      ->setMaxResults($limit);

    $conditions = $this->expressionFactory->createExpressionFromCriteria(
      $q->expr(),
      $criteria
    );

    if ($conditions) {
      $q->where($conditions);
    }

    $results = $q->execute();

    $ids = [];
    while ($variation_id = $results->fetchColumn(0)) {
      $ids[] = $variation_id;
    }

    return $ids;

  }

  /**
   * {@inheritdoc}
   */
  public function remove(Id $variation_id) {
    $this->storage->delete(array((string) $variation_id));
  }

}
