<?php

namespace Drupal\culturefeed_udb3;

use CultuurNet\UDB3\Iri\IriGeneratorInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManager;

/**
 * Class LocalFileIriGenerator.
 *
 * @package Drupal\culturefeed_udb3
 */
class LocalFileIriGenerator implements IriGeneratorInterface {

  /**
   * The stream wrapper manager.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManager
   */
  protected $streamWrapperManager;

  /**
   * @var String
   */
  private $publicDirectory;

  /**
   * LocalFileIriGenerator constructor.
   *
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManager $stream_wrapper_manager
   *   The stream wrapper manager.
   */
  public function __construct(StreamWrapperManager $stream_wrapper_manager, $public_directory) {
    $this->streamWrapperManager = $stream_wrapper_manager;
    $this->publicDirectory = $public_directory;
  }

  /**
   * {@inheritdoc}
   */
  public function iri($item) {
    $stream_wrapper = $this->streamWrapperManager->getViaUri($this->publicDirectory . '/' . $item);
    return $stream_wrapper ? $stream_wrapper->getExternalUrl() : false;
  }

}
