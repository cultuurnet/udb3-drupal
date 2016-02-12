<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\LocalFileSystemAdapterFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use League\Flysystem\Adapter\Local;

/**
 * Class LocalFileSystemAdapterFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class LocalFileSystemAdapterFactory {

  /**
   * Get the local filesystem adapter.
   *
   * @return \League\Flysystem\Adapter\Local
   *   The local file system adapter.
   */
  public function get() {
    return new Local(__DIR__);
  }

}
