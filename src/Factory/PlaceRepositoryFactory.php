<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\PlaceRepositoryFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnrichingEventStreamDecorator;
use CultuurNet\UDB3\OrganizerService;
use CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory;
use CultuurNet\UDB3\UDB2\Place\PlaceImporterInterface;
use CultuurNet\UDB3\UDB2\Place\PlaceRepository as UDB2PlaceRepository;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class PlaceRepositoryFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class PlaceRepositoryFactory implements PlaceRepositoryFactoryInterface {

  /**
   * The real place repository.
   *
   * @var \CultuurNet\UDB3\Place\PlaceRepository
   */
  protected $realPlaceRepository;

  /**
   * The entry api.
   *
   * @var \CultuurNet\UDB3\UDB2\EntryAPIFactory
   */
  protected $improvedEntryApi;

  /**
   * The place importer.
   *
   * @var \CultuurNet\UDB3\UDB2\Place\PlaceImporterInterface
   */
  protected $placeImporter;

  /**
   * The organizer service.
   *
   * @var \CultuurNet\UDB3\OrganizerService
   */
  protected $organizerService;

  /**
   * The event stream metadata enricher.
   *
   * @var MetadataEnrichingEventStreamDecorator
   */
  protected $eventStreamMetadataEnricher;

  /**
   * The config factory.
   *
   * @var ConfigFactory
   */
  protected $config;

  /**
   * Constructs an event repository factory.
   *
   * @param EventSourcingRepository $real_place_repository
   *   The real place repository.
   * @param EntryAPIImprovedFactory $improved_entry_api
   *   The improved entry api.
   * @param PlaceImporterInterface $place_importer
   *   The place importer.
   * @param OrganizerService $organizer_service
   *   The organizer service.
   * @param MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher
   *   The event stream metadata enricher.
   * @param ConfigFactory $config
   *   The config factory.
   */
  public function __construct(
    EventSourcingRepository $real_place_repository,
    EntryAPIImprovedFactory $improved_entry_api,
    PlaceImporterInterface $place_importer,
    OrganizerService $organizer_service,
    MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher,
    ConfigFactory $config
  ) {
    $this->realPlaceRepository = $real_place_repository;
    $this->improvedEntryApi = $improved_entry_api;
    $this->placeImporter = $place_importer;
    $this->organizerService = $organizer_service;
    $this->eventStreamMetadataEnricher = $event_stream_metadata_enricher;
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    $udb2_repository_decorator = new UDB2PlaceRepository(
      $this->realPlaceRepository,
      $this->improvedEntryApi,
      $this->placeImporter,
      $this->organizerService,
      array($this->eventStreamMetadataEnricher)
    );

    if ($this->config->get('sync_with_udb2')) {
      $udb2_repository_decorator->syncBackOn();
    }

    return $udb2_repository_decorator;

  }

}
