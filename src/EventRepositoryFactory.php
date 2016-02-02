<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\EventRepositoryFactory.
 */

namespace Drupal\culturefeed_udb3;

use Broadway\EventSourcing\MetadataEnrichment\MetadataEnrichingEventStreamDecorator;
use CultuurNet\UDB3\Event\EventRepository;
use CultuurNet\UDB3\OrganizerService;
use CultuurNet\UDB3\PlaceService;
use CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory;
use CultuurNet\UDB3\UDB2\EventImporterInterface;
use CultuurNet\UDB3\UDB2\EventRepository as EventRepository2;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class EventRepositoryFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class EventRepositoryFactory implements EventRepositoryFactoryInterface {

  /**
   * The local event repository.
   *
   * @var EventRepository
   */
  protected $localEventRepository;

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
   * @var OrganizerService
   */
  protected $organizerService;

  /**
   * @var PlaceService
   */
  protected $placeService;

  /**
   * Constructs an event repository factory.
   *
   * @param EventRepository $local_event_repository
   *   The local event repository.
   * @param EntryAPIImprovedFactory $improved_entry_api
   *   The improved entry api.
   * @param EventImporterInterface $event_importer
   * @param MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher
   *   The event stream metadata enricher.
   * @param ConfigFactory $config
   *   The config factory.
   */
  public function __construct(
    EventRepository $local_event_repository,
    EntryAPIImprovedFactory $improved_entry_api,
    EventImporterInterface $event_importer,
    PlaceService $place_service,
    OrganizerService $organizer_service,
    MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher,
    ConfigFactory $config
  ) {
    $this->localEventRepository = $local_event_repository;
    $this->improvedEntryApi = $improved_entry_api;
    $this->eventImporter = $event_importer;
    $this->organizerService = $organizer_service;
    $this->placeService = $place_service;
    $this->eventStreamMetadataEnricher = $event_stream_metadata_enricher;
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    $udb2_repository_decorator = new EventRepository2(
      $this->localEventRepository,
      $this->improvedEntryApi,
      $this->eventImporter,
      $this->placeService,
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
