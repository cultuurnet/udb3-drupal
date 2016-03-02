<?php

namespace Drupal\culturefeed_udb3\Cache;

use Drupal\Core\Cache\DatabaseBackend as CoreDatabaseBackend;

class DatabaseBackend extends CoreDatabaseBackend {

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    return NULL;
    // The udb3 json ld caches should not be cleared by drupal.  Only when
    // specifically asked.  Use realDeleteAll instead.
  }

  /**
   * Clears the cache bin.
   */
  public function realDeleteAll() {
    parent::deleteAll();
  }

}
