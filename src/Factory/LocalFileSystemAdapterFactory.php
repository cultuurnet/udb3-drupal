<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\LocalFileSystemAdapterFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use Drupal\Core\StreamWrapper\PublicStream;
use League\Flysystem\Adapter\Local;

/**
 * Class LocalFileSystemAdapterFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class LocalFileSystemAdapterFactory {

  /**
   * Get the callable iri generator.
   *
   * @return \League\Flysystem\Adapter\Local
   *   The local file system adapter.
   */
  public function get() {
    $file_system = PublicStream::baseUrl();
    return new Local($file_system);
  }

}
