<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\PlaceRepositoryFactory.
 */

namespace Drupal\culturefeed_udb3;

use Broadway\EventSourcing\EventSourcingRepository;
use CultuurNet\UDB3\SearchAPI2\DefaultSearchService;
use CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnrichingEventStreamDecorator;
use Drupal\Core\Config\ConfigFactory;
use CultuurNet\UDB3\UDB2\PlaceRepository as UDB2PlaceRepository;

/**
 * Class PlaceRepositoryFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class PlaceRepositoryFactory implements PlaceRepositoryFactoryInterface {

  /**
   * The local place repository.
   *
   * @var \CultuurNet\UDB3\Place\PlaceRepository
   */
  protected $localPlaceRepository;

  /**
   * The search api.
   *
   * @var \CultuurNet\UDB3\SearchAPI2\DefaultSearchService
   */
  protected $searchApi;

  /**
   * The entry api.
   *
   * @var \CultuurNet\UDB3\UDB2\EntryAPIFactory
   */
  protected $entryApi;

  /**
   * The event stream metadata enricher.
   *
   * @var \Broadway\EventSourcing\MetadataEnrichment\MetadataEnrichingEventStreamDecorator
   */
  protected $eventStreamMetadataEnricher;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
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
    MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher,
    ConfigFactory $config
  ) {
    $this->localPlaceRepository = $local_place_repository;
    $this->searchApi = $search_api;
    $this->improvedEntryApi = $improved_entry_api;
    $this->eventStreamMetadataEnricher = $event_stream_metadata_enricher;
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
      array($this->eventStreamMetadataEnricher)
    );

    if ($this->config->get('sync_with_udb2')) {
      $udb2_repository_decorator->syncBackOn();
    }

    return $udb2_repository_decorator;

  }

}
