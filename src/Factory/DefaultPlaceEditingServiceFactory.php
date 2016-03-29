<?php

namespace Drupal\culturefeed_udb3\Factory;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\Repository\RepositoryInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use CultuurNet\UDB3\Place\DefaultPlaceEditingService;
use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;
use CultuurNet\UDB3\Offer\Commands\OfferCommandFactoryInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class DefaultPlaceEditingServiceFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class DefaultPlaceEditingServiceFactory {

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
   * The place command factory.
   *
   * @var \CultuurNet\UDB3\Offer\Commands\OfferCommandFactoryInterface
   */
  protected $placeCommandFactory;

  /**
   * The place json ld repository.
   *
   * @var \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface
   */
  protected $placeJsonLdRepository;

  /**
   * The place repository.
   *
   * @var \Broadway\Repository\RepositoryInterface
   */
  protected $placeRepository;

  /**
   * The uuid generator.
   *
   * @var \Broadway\UuidGenerator\UuidGeneratorInterface
   */
  protected $uuidGenerator;

  /**
   * DefaultPlaceEditingServiceFactory constructor.
   *
   * @param \Broadway\CommandHandling\CommandBusInterface $command_bus
   *   The command bus.
   * @param \Broadway\UuidGenerator\UuidGeneratorInterface $uuid_generator
   *   The uuid generator.
   * @param \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface $place_json_ld_repository
   *   The place json ld repository.
   * @param \CultuurNet\UDB3\Offer\Commands\OfferCommandFactoryInterface $place_command_factory
   *   The place command factory.
   * @param \Broadway\Repository\RepositoryInterface $place_repository
   *   The place repository.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(
    CommandBusInterface $command_bus,
    UuidGeneratorInterface $uuid_generator,
    DocumentRepositoryInterface $place_json_ld_repository,
    OfferCommandFactoryInterface $place_command_factory,
    RepositoryInterface $place_repository,
    ConfigFactory $config
  ) {
    $this->commandBus = $command_bus;
    $this->uuidGenerator = $uuid_generator;
    $this->placeJsonLdRepository = $place_json_ld_repository;
    $this->placeCommandFactory = $place_command_factory;
    $this->placeRepository = $place_repository;
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * Get the default editing service.
   *
   * @return \CultuurNet\UDB3\Event\DefaultEventEditingService
   *   The default editing service.
   */
  public function get() {

    $editing_service = new DefaultPlaceEditingService(
      $this->commandBus,
      $this->uuidGenerator,
      $this->placeJsonLdRepository,
      $this->placeCommandFactory,
      $this->placeRepository
    );

    if ($this->config->get('publication_date')) {
      $publicationDate = \DateTimeImmutable::createFromFormat(\DateTime::ISO8601, $this->config->get('publication_date'));
      $editing_service = $editing_service->withFixedPublicationDateForNewOffers($publicationDate);
    }

    return $editing_service;

  }

}
