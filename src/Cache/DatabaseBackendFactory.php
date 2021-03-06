<?php

namespace Drupal\culturefeed_udb3\Cache;

use Drupal\Core\Cache\CacheFactoryInterface;
use Drupal\Core\Cache\CacheTagsChecksumInterface;
use Drupal\Core\Database\Connection;

/**
 * Class DatabaseBackendFactory.
 *
 * @package Drupal\culturefeed_udb3\Cache
 */
class DatabaseBackendFactory implements CacheFactoryInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The cache tags checksum provider.
   *
   * @var \Drupal\Core\Cache\CacheTagsChecksumInterface
   */
  protected $checksumProvider;

  /**
   * Constructs the DatabaseBackendFactory object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connection.
   * @param \Drupal\Core\Cache\CacheTagsChecksumInterface $checksum_provider
   *   The cache tags checksum provider.
   */
  public function __construct(Connection $connection, CacheTagsChecksumInterface $checksum_provider) {
    $this->connection = $connection;
    $this->checksumProvider = $checksum_provider;
  }

  /**
   * Gets DatabaseBackend for the specified cache bin.
   *
   * @param string $bin
   *   The cache bin for which the object is created.
   *
   * @return \Drupal\culturefeed_udb3\Cache\DatabaseBackend
   *   The cache backend object for the specified cache bin.
   */
  public function get($bin) {
    return new DatabaseBackend($this->connection, $this->checksumProvider, $bin);
  }

}
