<?php

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
   * The public stream wrapper.
   *
   * @var \Drupal\Core\StreamWrapper\PublicStream
   */
  private $publicStream;

  /**
   * The stream uri.
   *
   * @var string
   */
  protected $streamUri;

  /**
   * LocalFileSystemAdapterFactory constructor.
   *
   * @param \Drupal\Core\StreamWrapper\PublicStream $public_stream
   *   The public stream.
   * @param string $stream_uri
   *   The stream uri.
   */
  public function __construct(PublicStream $public_stream, $stream_uri) {
    $this->publicStream = $public_stream;
    $this->streamUri = $stream_uri;
  }


  /**
   * Get the public stream path.
   *
   * @return \League\Flysystem\Adapter\Local
   *   The local file system adapter.
   */
  public function get() {
    $this->publicStream->setUri($this->streamUri);
    return new Local($this->publicStream->realpath());
  }

}
