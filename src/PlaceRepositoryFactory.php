<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\PlaceRepositoryFactory.
 */

namespace Drupal\culturefeed_udb3;

use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnrichingEventStreamDecorator;
use CultuurNet\UDB3\OrganizerService;
use CultuurNet\UDB3\Place\PlaceRepository;
use CultuurNet\UDB3\UDB2\EntryAPIFactory;
use CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory;
use CultuurNet\UDB3\UDB2\Place\PlaceImporterInterface;
use CultuurNet\UDB3\UDB2\Place\PlaceRepository as UDB2PlaceRepository;
use Drupal\Core\Config\ConfigFactory;


/**
 * Class PlaceRepositoryFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class PlaceRepositoryFactory implements PlaceRepositoryFactoryInterface {

  /**
   * The local place repository.
   *
   * @var PlaceRepository
   */
  protected $localPlaceRepository;

  /**
   * The entry api.
   *
   * @var EntryAPIFactory
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
   * @param EventSourcingRepository $local_place_repository
   *   The local place repository.
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
    EventSourcingRepository $local_place_repository,
    EntryAPIImprovedFactory $improved_entry_api,
    PlaceImporterInterface $place_importer,
    OrganizerService $organizer_service,
    MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher,
    ConfigFactory $config
  ) {
    $this->localPlaceRepository = $local_place_repository;
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
      $this->localPlaceRepository,
      $this->improvedEntryApi,
      $this->placeImporter,
      $this->organizerService,
      array($this->eventStreamMetadataEnricher)
    );

    if ($this->config->get('sync_with_udb2')) {
      $udb2_repository_decorator->syncBackOn();
    }

    if ($this->config->get('use_full_event_data_to_update_description')) {
      $udb2_repository_decorator = $udb2_repository_decorator->withFullEventDataToUpdateDescription();
    }

    return $udb2_repository_decorator;

  }

}
