<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator;
use CultuurNet\UDB3\Event\ReadModel\JSONLD\EventFactory;
use CultuurNet\UDB3\SimpleEventBus;
use Drupal\culturefeed_udb3\Repository\CacheDocumentRepository;

/**
 * Class EventJsonLdRepositoryFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class EventJsonLdRepositoryFactory {

  /**
   * The event bus.
   *
   * @var \CultuurNet\UDB3\SimpleEventBus
   */
  protected $eventBus;

  /**
   * The event cache document repository.
   *
   * @var \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository
   */
  protected $eventCacheDocumentRepository;

  /**
   * EventJsonLdRepositoryFactory constructor.
   *
   * @param \CultuurNet\UDB3\SimpleEventBus $event_bus
   *   The event bus.
   * @param \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository $event_cache_document_repository
   *   The event cache document repository.
   */
  public function __construct(SimpleEventBus $event_bus, CacheDocumentRepository $event_cache_document_repository) {
    $this->eventBus = $event_bus;
    $this->eventCacheDocumentRepository = $event_cache_document_repository;
  }

  /**
   * Get the event json ld repository.
   *
   * @return \CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator
   *   The event json ld repository.
   */
  public function get() {

    return new BroadcastingDocumentRepositoryDecorator(
      $this->eventCacheDocumentRepository,
      $this->eventBus,
      new EventFactory()
    );

  }

}
