<?php

namespace Drupal\culturefeed_udb3\Factory;

use Broadway\Repository\RepositoryInterface;
use CultuurNet\UDB3\Offer\BulkLabelCommandHandler;
use CultuurNet\UDB3\Offer\OfferType;
use CultuurNet\UDB3\Search\ResultsGenerator;

/**
 * Class BulkLabelCommandHandlerFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class BulkLabelCommandHandlerFactory {

  /**
   * The event repository.
   *
   * @var RepositoryInterface
   */
  protected $eventRepository;

  /**
   * The place repository.
   *
   * @var RepositoryInterface
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
   * @param RepositoryInterface $event_repository
   *   The event repository.
   * @param RepositoryInterface $place_repository
   *   The place repository.
   */
  public function __construct(
    ResultsGenerator $search_results_generator,
    RepositoryInterface $event_repository,
    RepositoryInterface $place_repository
  ) {
    $this->searchResultsGenerator = $search_results_generator;
    $this->eventRepository = $event_repository;
    $this->placeRepository = $place_repository;
  }

  /**
   * Get the place json ld repository.
   *
   * @return \CultuurNet\UDB3\ReadModel\BroadcastingDocumentRepositoryDecorator
   *   The place json ld repository.
   */
  public function get() {
    return (new BulkLabelCommandHandler($this->searchResultsGenerator))
      ->withRepository(OfferType::EVENT(), $this->eventRepository)
      ->withRepository(OfferType::PLACE(), $this->placeRepository);
  }

}
