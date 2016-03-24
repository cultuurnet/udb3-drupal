<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\CommandHandling\ResqueCommandBus;
use CultuurNet\UDB3\EntityServiceInterface;
use CultuurNet\UDB3\Offer\IriOfferIdentifierFactoryInterface;
use CultuurNet\UDB3\Offer\OfferType;
use CultuurNet\UDB3\Symfony\CommandDeserializerController;
use CultuurNet\UDB3\Variations\Command\CreateOfferVariationJSONDeserializer;
use CultuurNet\UDB3\Variations\Model\Properties\DefaultUrlValidator;

/**
 * Class VariationsWriteControllerFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class VariationsWriteControllerFactory {

  /**
   * The command bus.
   *
   * @var \CultuurNet\UDB3\CommandHandling\ResqueCommandBus
   */
  protected $commandBus;

  /**
   * The event service.
   *
   * @var \CultuurNet\UDB3\EntityServiceInterface
   */
  protected $eventService;

  /**
   * The iri offer identifier factory.
   *
   * @var \CultuurNet\UDB3\Offer\IriOfferIdentifierFactoryInterface
   */
  protected $iriOfferIdentifierFactory;

  /**
   * The place service.
   *
   * @var \CultuurNet\UDB3\EntityServiceInterface
   */
  protected $placeService;

  /**
   * VariationsWriteControllerFactory constructor.
   *
   * @param \CultuurNet\UDB3\Offer\IriOfferIdentifierFactoryInterface $iri_offer_identifier_factory
   *   The iri offer identifier factory.
   * @param \CultuurNet\UDB3\EntityServiceInterface $event_service
   *   The event service.
   * @param \CultuurNet\UDB3\EntityServiceInterface $place_service
   *   The place service.
   * @param \CultuurNet\UDB3\CommandHandling\ResqueCommandBus $command_bus
   *   The command bus.
   */
  public function __construct(
    IriOfferIdentifierFactoryInterface $iri_offer_identifier_factory,
    EntityServiceInterface $event_service,
    EntityServiceInterface $place_service,
    ResqueCommandBus $command_bus
   ) {
    $this->iriOfferIdentifierFactory = $iri_offer_identifier_factory;
    $this->eventService = $event_service;
    $this->placeService = $place_service;
    $this->commandBus = $command_bus;
  }

  /**
   * Get the variations write controller.
   *
   * @return \CultuurNet\UDB3\Offer\IriOfferIdentifierFactory
   *   The iri offer identifier factory.
   */
  public function get() {

    $urlValidator = (new DefaultUrlValidator($this->iriOfferIdentifierFactory))
      ->withEntityService(OfferType::EVENT(), $this->eventService)
      ->withEntityService(OfferType::PLACE(), $this->placeService);

    $deserializer = new CreateOfferVariationJSONDeserializer();
    $deserializer->addUrlValidator(
      $urlValidator
    );

    return new CommandDeserializerController(
      $deserializer,
      $this->commandBus
    );

  }

}
