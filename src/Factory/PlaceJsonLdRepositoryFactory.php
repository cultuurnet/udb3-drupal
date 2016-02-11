<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\PlaceJsonLdRepositoryFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator;
use CultuurNet\UDB3\Place\ReadModel\JSONLD\EventFactory;
use CultuurNet\UDB3\SimpleEventBus;
use Drupal\culturefeed_udb3\Repository\CacheDocumentRepository;

/**
 * Class EventJsonLdRepositoryFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class PlaceJsonLdRepositoryFactory {

  /**
   * The event bus.
   *
   * @var \CultuurNet\UDB3\SimpleEventBus
   */
  protected $eventBus;

  /**
   * The place cache document repository.
   *
   * @var \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository
   */
  protected $placeCacheDocumentRepository;

  /**
   * EventJsonLdRepositoryFactory constructor.
   *
   * @param \CultuurNet\UDB3\SimpleEventBus $event_bus
   *   The event bus.
   * @param \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository $place_cache_document_repository
   *   The place cache document repository.
   */
  public function __construct(SimpleEventBus $event_bus, CacheDocumentRepository $place_cache_document_repository) {
    $this->eventBus = $event_bus;
    $this->placeCacheDocumentRepository = $place_cache_document_repository;
  }

  /**
   * Get the place json ld repository.
   *
   * @return \CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator
   *   The place json ld repository.
   */
  public function get() {

    return new BroadcastingDocumentRepositoryDecorator(
      $this->placeCacheDocumentRepository,
      $this->eventBus,
      new EventFactory()
    );

  }

}
