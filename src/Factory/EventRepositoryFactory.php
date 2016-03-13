<?php

namespace Drupal\culturefeed_udb3\Factory;

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
   * The config factory.
   *
   * @var ConfigFactory
   */
  protected $config;

  /**
   * The event importer.
   *
   * @var \CultuurNet\UDB3\UDB2\EventImporterInterface
   */
  protected $eventImporter;

  /**
   * The event stream metadata enricher.
   *
   * @var MetadataEnrichingEventStreamDecorator
   */
  protected $eventStreamMetadataEnricher;

  /**
   * The improved entry api.
   *
   * @var \CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory
   */
  protected $improvedEntryApi;

  /**
   * The local event repository.
   *
   * @var EventRepository
   */
  protected $localEventRepository;

  /**
   * The organizer service.
   *
   * @var OrganizerService
   */
  protected $organizerService;

  /**
   * The place service.
   *
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
   *   The event importer.
   * @param PlaceService $place_service
   *   The place service.
   * @param OrganizerService $organizer_service
   *   The organizer service.
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

    return $udb2_repository_decorator;
  }

}
