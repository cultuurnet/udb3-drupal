<?php

/**
 * @file
 * Contains Drupal\...\Factory\CommandDeserializerControllerFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\LocalEventService;
use CultuurNet\UDB3\Variations\Command\CreateEventVariationJSONDeserializer;
use CultuurNet\UDB3\Variations\Model\Properties\DefaultUrlValidator;
use CultuurNet\UDB3\Symfony\CommandDeserializerController;
use CultuurNet\UDB3\CommandHandling\ResqueCommandBus;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class CommandDeserializerControllerFactory
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class CommandDeserializerControllerFactory {

  /**
   * The command bus.
   *
   * @var \CultuurNet\UDB3\CommandHandling\ResqueCommandBus
   */
  protected $commandBus;

  /**
   * The event service.
   *
   * @var \CultuurNet\UDB3\LocalEventService
   */
  protected $eventService;

  /**
   * The event url regex.
   *
   * @var string
   */
  protected $eventUrlRegex;

  /**
   * CommandDeserializerControllerFactory constructor.
   *
   * @param \CultuurNet\UDB3\LocalEventService $event_service
   *   The event service.
   * @param \CultuurNet\UDB3\CommandHandling\ResqueCommandBus $command_bus
   *   The command bus.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(LocalEventService $event_service, ResqueCommandBus $command_bus, ConfigFactory $config) {
    $this->eventService = $event_service;
    $this->commandBus = $command_bus;
    $this->eventUrlRegex = $config->get('culturefeed_udb3.settings')->get('event_url_regex');
  }
  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Get the command deserializer controller.
   *
   * @return \CultuurNet\UDB3\Symfony\CommandDeserializerController
   *   The command deserializer controller.
   */
  public function get() {

    $deserializer = new CreateEventVariationJSONDeserializer();
    $deserializer->addUrlValidator(
      new DefaultUrlValidator(
        $this->eventUrlRegex,
        $this->eventService
      )
    );

    return new CommandDeserializerController(
      $deserializer,
      $this->commandBus
    );

  }

}
