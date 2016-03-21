<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;
use CultuurNet\UDB3\Offer\IriOfferIdentifierFactoryInterface;
use CultuurNet\UDB3\Offer\LocalOfferReadingService;
use CultuurNet\UDB3\Offer\OfferType;

/**
 * Class OfferReadingServcieFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class OfferReadingServiceFactory {

  /**
   * The event json ld repository.
   *
   * @var \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface
   */
  protected $eventJsonLdRepository;

  /**
   * The iri offer identifier factory.
   *
   * @var \CultuurNet\UDB3\Offer\IriOfferIdentifierFactoryInterface
   */
  protected $iriOfferIdentifierFactory;

  /**
   * The place json ld repository.
   *
   * @var \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface
   */
  protected $placeJsonLdRepository;

  /**
   * OfferReadingServiceFactory constructor.
   *
   * @param \CultuurNet\UDB3\Offer\IriOfferIdentifierFactoryInterface $iri_offer_identifier_factory
   *   The iri offer identifier factory.
   * @param \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface $event_json_ld_repository
   *   The event json ld repository.
   * @param \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface $place_json_ld_repository
   *   The place json ld repository.
   */
  public function __construct(
    IriOfferIdentifierFactoryInterface $iri_offer_identifier_factory,
    DocumentRepositoryInterface $event_json_ld_repository,
    DocumentRepositoryInterface $place_json_ld_repository
  ) {
    $this->iriOfferIdentifierFactory = $iri_offer_identifier_factory;
    $this->eventJsonLdRepository = $event_json_ld_repository;
    $this->placeJsonLdRepository = $place_json_ld_repository;
  }

  /**
   * Get the local offer reading service.
   *
   * @return \CultuurNet\UDB3\Offer\LocalOfferReadingService|static
   *   The local offer reading service.
   */
  public function get() {

    $localOfferReadingService = new LocalOfferReadingService($this->iriOfferIdentifierFactory);
    $localOfferReadingService = $localOfferReadingService->withDocumentRepository(OfferType::EVENT(), $this->eventJsonLdRepository);
    $localOfferReadingService = $localOfferReadingService->withDocumentRepository(OfferType::PLACE(), $this->placeJsonLdRepository);

    return $localOfferReadingService;

  }

}
