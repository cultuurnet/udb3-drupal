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
   * LocalFileIriGenerator constructor.
   *
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManager $stream_wrapper_manager
   *   The stream wrapper manager.
   */
  public function __construct(StreamWrapperManager $stream_wrapper_manager) {
    $this->streamWrapperManager = $stream_wrapper_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function iri($item) {

    $stream_wrapper = $this->streamWrapperManager->getViaUri('public://downloads/' . $item);
    return $stream_wrapper->getExternalUrl();
  }

}
