<?php

namespace Drupal\culturefeed_udb3\Factory;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\Repository\RepositoryInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use CultuurNet\UDB3\Event\DefaultEventEditingService;
use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;
use CultuurNet\UDB3\EventServiceInterface;
use CultuurNet\UDB3\Offer\Commands\OfferCommandFactoryInterface;
use CultuurNet\UDB3\PlaceService;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class DefaultEventEditingServiceFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class DefaultEventEditingServiceFactory {

  /**
   * The command bus.
   *
   * @var \Broadway\CommandHandling\CommandBusInterface
   */
  protected $commandBus;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * The event command factory.
   *
   * @var \CultuurNet\UDB3\Offer\Commands\OfferCommandFactoryInterface
   */
  protected $eventCommandFactory;

  /**
   * The event json ld repository.
   *
   * @var \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface
   */
  protected $eventJsonLdRepository;

  /**
   * The event repository.
   *
   * @var \Broadway\Repository\RepositoryInterface
   */
  protected $eventRepository;

  /**
   * The event service.
   *
   * @var \CultuurNet\UDB3\EventServiceInterface
   */
  protected $eventService;

  /**
   * The place service.
   *
   * @var \CultuurNet\UDB3\PlaceService
   */
  protected $placeService;

  /**
   * The uuid generator.
   *
   * @var \Broadway\UuidGenerator\UuidGeneratorInterface
   */
  protected $uuidGenerator;

  /**
   * DefaultEventEditingServiceFactory constructor.
   *
   * @param \CultuurNet\UDB3\EventServiceInterface $event_service
   *   The event service.
   * @param \Broadway\CommandHandling\CommandBusInterface $command_bus
   *   The command bus.
   * @param \Broadway\UuidGenerator\UuidGeneratorInterface $uuid_generator
   *   The uuid generator.
   * @param \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface $event_json_ld_repository
   *   The event json ld repository.
   * @param \CultuurNet\UDB3\PlaceService $place_service
   *   The place service.
   * @param \CultuurNet\UDB3\Offer\Commands\OfferCommandFactoryInterface $event_command_factory
   *   The event command factory.
   * @param \Broadway\Repository\RepositoryInterface $event_repository
   *   The event repository.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(
    EventServiceInterface $event_service,
    CommandBusInterface $command_bus,
    UuidGeneratorInterface $uuid_generator,
    DocumentRepositoryInterface $event_json_ld_repository,
    PlaceService $place_service,
    OfferCommandFactoryInterface $event_command_factory,
    RepositoryInterface $event_repository,
    ConfigFactory $config
  ) {
    $this->eventService = $event_service;
    $this->commandBus = $command_bus;
    $this->uuidGenerator = $uuid_generator;
    $this->eventJsonLdRepository = $event_json_ld_repository;
    $this->placeService = $place_service;
    $this->eventCommandFactory = $event_command_factory;
    $this->eventRepository = $event_repository;
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * Get the default editing service.
   *
   * @return \CultuurNet\UDB3\Event\DefaultEventEditingService
   *   The default editing service.
   */
  public function get() {

    $editing_service = new DefaultEventEditingService(
      $this->eventService,
      $this->commandBus,
      $this->uuidGenerator,
      $this->eventJsonLdRepository,
      $this->placeService,
      $this->eventCommandFactory,
      $this->eventRepository
    );

    if ($this->config->get('publication_date')) {
      $publicationDate = \DateTimeImmutable::createFromFormat(\DateTime::ISO8601, $this->config->get('publication_date'));
      $editing_service = $editing_service->withFixedPublicationDateForNewOffers($publicationDate);
    }

    return $editing_service;

  }

}
