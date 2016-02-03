<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\CallableIriGeneratorFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Iri\CallableIriGenerator;
use Drupal\Core\StreamWrapper\PublicStream;

/**
 * Class CallableIriGeneratorFactory
 * @package Drupal\culturefeed_udb3\Factory
 */
class CallableIriGeneratorFactory {


  /**
   * Get the callable iri generator.
   *
   * @return \CultuurNet\UDB3\Iri\CallableIriGenerator
   *   The callable iri generator.
   */
  public function get() {
    $file_system = PublicStream::baseUrl();
    return new CallableIriGenerator(

      function ($filePath) use ($file_system) {
        return $file_system . '/culturefeed/media/' . $filePath;
      }
    );
  }

}
