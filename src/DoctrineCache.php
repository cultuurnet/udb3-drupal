<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\DoctrineCache.
 */

namespace Drupal\culturefeed_udb3;

use Doctrine\Common\Cache\Cache;
use Drupal\Core\Cache\CacheBackendInterface;

/**
 * Class DoctrineCache.
 *
 * @package Drupal\culturefeed_udb3
 */
class DoctrineCache implements Cache {

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
   * {@inheritdoc}
   */
  public function fetch($id) {
    $cache = $this->cache->get($id);
    return ($cache) ? $cache->data : FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function save($id, $data, $life_time = 0) {
    $this->cache->set($id, $data);
  }

  /**
   * {@inheritdoc}
   */
  public function delete($id) {
    $this->cache->delete($id);
  }

  /**
   * {@inheritdoc}
   */
  public function contains($id) {
    return ($this->cache->get($id));
  }

  /**
   * {@inheritdoc}
   */
  public function getStats() {
    return NULL;
  }

}
