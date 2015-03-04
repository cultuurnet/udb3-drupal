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
use CultuurNet\UDB3\SearchAPI2\DefaultSearchService;
use CultuurNet\UDB3\UDB2\EntryAPIFactory;
use CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory;
use CultuurNet\UDB3\UDB2\PlaceRepository as UDB2PlaceRepository;
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
   * The search api.
   *
   * @var DefaultSearchService
   */
  protected $searchApi;

  /**
   * The entry api.
   *
   * @var EntryAPIFactory
   */
  protected $entryApi;

  /**
   * The event stream metadata enricher.
   *
   * @var MetadataEnrichingEventStreamDecorator
   */
  protected $eventStreamMetadataEnricher;

  /**
   * @var OrganizerService
   */
  protected $organizerService;

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
   * @param DefaultSearchService $search_api
   *   The search api.
   * @param EntryAPIImprovedFactory $improved_entry_api
   *   The improved entry api.
   * @param MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher
   *   The event stream metadata enricher.
   * @param ConfigFactory $config
   *   The config factory.
   */
  public function __construct(
    EventSourcingRepository $local_place_repository,
    DefaultSearchService $search_api,
    EntryAPIImprovedFactory $improved_entry_api,
    OrganizerService $organizer_service,
    MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher,
    ConfigFactory $config
  ) {
    $this->localPlaceRepository = $local_place_repository;
    $this->searchApi = $search_api;
    $this->improvedEntryApi = $improved_entry_api;
    $this->eventStreamMetadataEnricher = $event_stream_metadata_enricher;
    $this->organizerService = $organizer_service;
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    $udb2_repository_decorator = new UDB2PlaceRepository(
      $this->localPlaceRepository,
      $this->searchApi,
      $this->improvedEntryApi,
      $this->organizerService,
      array($this->eventStreamMetadataEnricher)
    );

    if ($this->config->get('sync_with_udb2')) {
      $udb2_repository_decorator->syncBackOn();
    }

    return $udb2_repository_decorator;

  }

}
