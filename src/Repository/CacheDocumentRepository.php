<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Repository\CacheDocumentRepository.
 */

namespace Drupal\culturefeed_udb3\Repository;

use CultuurNet\UDB3\Event\ReadModel\DocumentGoneException;
use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;
use CultuurNet\UDB3\ReadModel\JsonDocument;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Class CacheDocumentRepository.
 *
 * @package Drupal\culturefeed_udb3\Repository
 */
class CacheDocumentRepository implements DocumentRepositoryInterface {

  /**
   * The cache backend.
   *
   * @var CacheBackendInterface
   */
  protected $cache;

  /**
   * CacheDocumentRepository constructor.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache
   *   The cache.
   */
  public function __construct(CacheBackendInterface $cache) {
    $this->cache = $cache;
  }

  /**
   * {@inheritdoc}
   */
  public function get($id) {

    $cache = $this->cache->get((string) $id);
    $value = ($cache) ? $cache->data : NULL;

    if ('GONE' === $value) {
      throw new DocumentGoneException();
    }

    if (empty($value)) {
      return NULL;
    }

    return new JsonDocument((string) $id, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function save(JsonDocument $document) {
    $this->cache->set((string) $document->getId(), $document->getRawBody(), CacheBackendInterface::CACHE_PERMANENT);
  }

  /**
   * {@inheritdoc}
   */
  public function remove($id) {
    $this->cache->set((string) $id, 'GONE', CacheBackendInterface::CACHE_PERMANENT);
  }

}
