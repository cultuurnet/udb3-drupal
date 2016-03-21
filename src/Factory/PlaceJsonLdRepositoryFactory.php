<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Iri\IriGeneratorInterface;
use CultuurNet\UDB3\Place\ReadModel\JSONLD\EventFactory;
use CultuurNet\UDB3\ReadModel\BroadcastingDocumentRepositoryDecorator;
use CultuurNet\UDB3\SimpleEventBus;
use Drupal\culturefeed_udb3\Repository\CacheDocumentRepository;

/**
 * Class PlaceJsonLdRepositoryFactory.
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
   * The place iri generator.
   *
   * @var \CultuurNet\UDB3\Iri\IriGeneratorInterface
   */
  protected $placeIriGenerator;

  /**
   * PlaceJsonLdRepositoryFactory constructor.
   *
   * @param \CultuurNet\UDB3\SimpleEventBus $event_bus
   *   The event bus.
   * @param \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository $place_cache_document_repository
   *   The place cache document repository.
   * @param \CultuurNet\UDB3\Iri\IriGeneratorInterface $place_iri_generator
   *   The place iri generator.
   */
  public function __construct(
    SimpleEventBus $event_bus,
    CacheDocumentRepository $place_cache_document_repository,
    IriGeneratorInterface $place_iri_generator
  ) {
    $this->eventBus = $event_bus;
    $this->placeCacheDocumentRepository = $place_cache_document_repository;
    $this->placeIriGenerator = $place_iri_generator;
  }

  /**
   * Get the place json ld repository.
   *
   * @return \CultuurNet\UDB3\ReadModel\BroadcastingDocumentRepositoryDecorator
   *   The place json ld repository.
   */
  public function get() {

    return new BroadcastingDocumentRepositoryDecorator(
      $this->placeCacheDocumentRepository,
      $this->eventBus,
      new EventFactory($this->placeIriGenerator)
    );

  }

}
