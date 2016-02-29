<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Offer\BulkLabelCommandHandler;
use CultuurNet\UDB3\Offer\OfferType;
use CultuurNet\UDB3\Search\ResultsGenerator;
use CultuurNet\UDB3\UDB2\EventRepository;
use CultuurNet\UDB3\UDB2\Place\PlaceRepository;

/**
 * Class BulkLabelCommandHandlerFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class BulkLabelCommandHandlerFactory {

  /**
   * The event repository.
   *
   * @var \CultuurNet\UDB3\UDB2\EventRepository
   */
  protected $eventRepository;

  /**
   * The place repository.
   *
   * @var \CultuurNet\UDB3\UDB2\Place\PlaceRepository
   */
  protected $placeRepository;

  /**
   * The search results generator.
   *
   * @var \CultuurNet\UDB3\Search\ResultsGenerator
   */
  protected $searchResultsGenerator;


  /**
   * BulkLabelCommandHandlerFactory constructor.
   *
   * @param \CultuurNet\UDB3\Search\ResultsGenerator $search_results_generator
   *   The search results generator.
   * @param \CultuurNet\UDB3\UDB2\EventRepository $event_repository
   *   The event repository.
   * @param \CultuurNet\UDB3\UDB2\Place\PlaceRepository $place_repository
   *   The place repository.
   */
  public function __construct(
    ResultsGenerator $search_results_generator,
    EventRepository $event_repository,
    PlaceRepository $place_repository
  ) {
    $this->searchResultsGenerator = $search_results_generator;
    $this->eventRepository = $event_repository;
    $this->placeRepository = $place_repository;
  }

  /**
   * Get the place json ld repository.
   *
   * @return \CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator
   *   The place json ld repository.
   */
  public function get() {

    $bulk_command_handler = new BulkLabelCommandHandler($this->searchResultsGenerator);
    $bulk_command_handler = $bulk_command_handler->withRepository(OfferType::EVENT(), $this->eventRepository);
    $bulk_command_handler = $bulk_command_handler->withRepository(OfferType::PLACE(), $this->placeRepository);
    return $bulk_command_handler;

  }

}
