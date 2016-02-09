<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\DoctrineCache.
 */

namespace Drupal\culturefeed_udb3;

use Drupal\Core\Cache\CacheBackendInterface;

class DoctrineCache implements \Doctrine\Common\Cache\Cache
{

  /**
   * The cache backend.
   *
   * @var CacheBackendInterface
   */
  protected $cache;

  /**
   * Constructs a new Cache.
   *
   * @param CacheBackendInterface $cache
   *   The cache backend.
   */
  public function __construct(
    CacheBackendInterface $cache
  ) {
    $this->cache = $cache;
  }

  /**
   * @inheritdoc
   */
  public function fetch($id) {
    $this->cache->get($id);
  }

  /**
   * @inheritdoc
   */
  public function save($id, $data, $lifeTime = 0) {
    $this->cache->set($id, $data);
  }

  /**
   * @inheritdoc
   */
  public function delete($id) {
    $this->cache->delete($id);
  }

  /**
   * @inheritdoc
   */
  public function contains($id) {
    return $this->cache->get($id);
  }

  /**
   * @inheritdoc
   */
  public function getStats() {
    return NULL;
  }

}
