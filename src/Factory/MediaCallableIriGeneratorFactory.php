<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Iri\CallableIriGenerator;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\StreamWrapper\PublicStream;

/**
 * Class MediaCallableIriGeneratorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class MediaCallableIriGeneratorFactory {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory;
   */
  protected $config;

  /**
   * The media directory.
   *
   * @var String
   */
  protected $mediaDirectory;

  /**
   * The public stream.
   *
   * @var \Drupal\Core\StreamWrapper\PublicStream
   */
  protected $publicStream;

  /**
   * MediaCallableIriGeneratorFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   * @param \Drupal\Core\StreamWrapper\PublicStream $public_stream
   *   The public stream.
   * @param string $media_directory
   *   The media directory.
   */
  public function __construct(ConfigFactory $config, PublicStream $public_stream, $media_directory) {
    $this->config = $config->get('culturefeed_udb3.settings');
    $this->publicStream = $public_stream;
    $this->mediaDirectory = $media_directory;
  }

  /**
   * Get the callable iri generator.
   *
   * @return \CultuurNet\UDB3\Iri\CallableIriGenerator
   *   The callable iri generator.
   */
  public function get() {

    $url = $this->config->get('url');
    $base_path = $this->publicStream->basePath();
    $base_url = $url . '/' . $base_path . '/' . $this->mediaDirectory;

    return new CallableIriGenerator(
      function ($file_path) use ($base_url) {
        return $base_url . '/' . $file_path;
      }
    );

  }

}
