<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\EventRepositoryFactory.
 */

namespace Drupal\culturefeed_udb3;

use Broadway\EventSourcing\MetadataEnrichment\MetadataEnrichingEventStreamDecorator;
use CultuurNet\UDB3\Event\EventRepository;
use CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory;
use CultuurNet\UDB3\UDB2\EventImporterInterface;
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
    MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher,
    ConfigFactory $config
  ) {
    $this->localEventRepository = $local_event_repository;
    $this->improvedEntryApi = $improved_entry_api;
    $this->eventImporter = $event_importer;
    $this->eventStreamMetadataEnricher = $event_stream_metadata_enricher;
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    $udb2_repository_decorator = new \CultuurNet\UDB3\UDB2\EventRepository(
      $this->localEventRepository,
      $this->improvedEntryApi,
      $this->eventImporter,
      array($this->eventStreamMetadataEnricher)
    );

    if ($this->config->get('sync_with_udb2')) {
      $udb2_repository_decorator->syncBackOn();
    }

    return $udb2_repository_decorator;
  }

}
