<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\OrganizerRepositoryFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventSourcing\MetadataEnrichment\MetadataEnrichingEventStreamDecorator;
use Drupal\Core\Config\ConfigFactory;
use CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory;
use CultuurNet\UDB3\UDB2\Organizer\OrganizerImporterInterface;
use CultuurNet\UDB3\UDB2\Organizer\OrganizerRepository as UDB2OrganizeRepository;

/**
 * Class OrganizerRepositoryFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class OrganizerRepositoryFactory implements OrganizerRepositoryFactoryInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * The event stream metadata enricher.
   *
   * @var \Broadway\EventSourcing\MetadataEnrichment\MetadataEnrichingEventStreamDecorator
   */
  protected $eventStreamMetadataEnricher;

  /**
   * The entry api.
   *
   * @var \CultuurNet\UDB3\UDB2\EntryAPIFactory
   */
  protected $improvedEntryApi;

  /**
   * The organizer importer.
   *
   * @var \CultuurNet\UDB3\UDB2\Organizer\OrganizerImporterInterface
   */
  protected $organizerImporter;

  /**
   * The real organizer repository.
   *
   * @var \CultuurNet\UDB3\Organizer\OrganizerRepository
   */
  protected $realOrganizerRepository;

  /**
   * Constructs an event repository factory.
   *
   * @param EventSourcingRepository $real_organizer_repository
   *   The local organizer repository.
   * @param EntryAPIImprovedFactory $improved_entry_api
   *   The improved entry api.
   * @param OrganizerImporterInterface $organizer_importer
   *   The organizer importer.
   * @param MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher
   *   The event stream metadata enricher.
   * @param ConfigFactory $config
   *   The config factory.
   */
  public function __construct(
    EventSourcingRepository $real_organizer_repository,
    EntryAPIImprovedFactory $improved_entry_api,
    OrganizerImporterInterface $organizer_importer,
    MetadataEnrichingEventStreamDecorator $event_stream_metadata_enricher,
    ConfigFactory $config
  ) {
    $this->realOrganizerRepository = $real_organizer_repository;
    $this->improvedEntryApi = $improved_entry_api;
    $this->organizerImporter = $organizer_importer;
    $this->eventStreamMetadataEnricher = $event_stream_metadata_enricher;
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    $udb2_repository_decorator = new UDB2OrganizeRepository(
      $this->realOrganizerRepository,
      $this->improvedEntryApi,
      $this->organizerImporter,
      array($this->eventStreamMetadataEnricher)
    );

    if ($this->config->get('sync_with_udb2')) {
      $udb2_repository_decorator->syncBackOn();
    }

    return $udb2_repository_decorator;

  }

}
