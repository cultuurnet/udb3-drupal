<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\OrganizerJsonLdRepositoryFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator;
use CultuurNet\UDB3\Place\ReadModel\JSONLD\EventFactory;
use CultuurNet\UDB3\SimpleEventBus;
use Drupal\culturefeed_udb3\Repository\CacheDocumentRepository;

/**
 * Class OrganizerJsonLdRepositoryFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class OrganizerJsonLdRepositoryFactory {

  /**
   * The event bus.
   *
   * @var \CultuurNet\UDB3\SimpleEventBus
   */
  protected $eventBus;

  /**
   * The organizer cache document repository.
   *
   * @var \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository
   */
  protected $organizerCacheDocumentRepository;

  /**
   * OrganizerJsonLdRepositoryFactory constructor.
   *
   * @param \CultuurNet\UDB3\SimpleEventBus $event_bus
   *   The event bus.
   * @param \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository $organizer_cache_document_repository
   *   The place cache document repository.
   */
  public function __construct(SimpleEventBus $event_bus, CacheDocumentRepository $organizer_cache_document_repository) {
    $this->eventBus = $event_bus;
    $this->organizerCacheDocumentRepository = $organizer_cache_document_repository;
  }

  /**
   * Get the organizer json ld repository.
   *
   * @return \CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator
   *   The organizer json ld repository.
   */
  public function get() {

    return new BroadcastingDocumentRepositoryDecorator(
      $this->organizerCacheDocumentRepository,
      $this->eventBus,
      new EventFactory()
    );

  }

}
