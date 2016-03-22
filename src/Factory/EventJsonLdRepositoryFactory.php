<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Event\ReadModel\JSONLD\EventFactory;
use CultuurNet\UDB3\Iri\IriGeneratorInterface;
use CultuurNet\UDB3\ReadModel\BroadcastingDocumentRepositoryDecorator;
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
   * The event iri generator.
   *
   * @var \CultuurNet\UDB3\Iri\IriGeneratorInterface
   */
  protected $eventIriGenerator;

  /**
   * EventJsonLdRepositoryFactory constructor.
   *
   * @param \CultuurNet\UDB3\SimpleEventBus $event_bus
   *   The event bus.
   * @param \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository $event_cache_document_repository
   *   The event cache document repository.
   * @param \CultuurNet\UDB3\Iri\IriGeneratorInterface $event_iri_generator
   *   The event iri generator.
   */
  public function __construct(
    SimpleEventBus $event_bus,
    CacheDocumentRepository $event_cache_document_repository,
    IriGeneratorInterface $event_iri_generator
  ) {
    $this->eventBus = $event_bus;
    $this->eventCacheDocumentRepository = $event_cache_document_repository;
    $this->eventIriGenerator = $event_iri_generator;
  }

  /**
   * Get the event json ld repository.
   *
   * @return \CultuurNet\UDB3\ReadModel\BroadcastingDocumentRepositoryDecorator
   *   The event json ld repository.
   */
  public function get() {

    return new BroadcastingDocumentRepositoryDecorator(
      $this->eventCacheDocumentRepository,
      $this->eventBus,
      new EventFactory($this->eventIriGenerator)
    );

  }

}
