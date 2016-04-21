<?php

namespace Drupal\culturefeed_udb3\Factory;

use Broadway\EventHandling\EventBusInterface;
use CultuurNet\BroadwayAMQP\EventBusForwardingConsumerFactory;
use CultuurNet\Deserializer\DeserializerLocatorInterface;
use Drupal\Core\Config\ConfigFactory;
use Psr\Log\LoggerInterface;
use ValueObjects\Number\Natural;

/**
 * Class EventBusForwardingConsumerFactoryFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class EventBusForwardingConsumerFactoryFactory {

  /**
   * The connection config.
   *
   * @var array
   */
  protected $connectionConfig;

  /**
   * The deserializer locator.
   *
   * @var \CultuurNet\Deserializer\DeserializerLocatorInterface
   */
  protected $deserializerLocator;

  /**
   * The event bus.
   *
   * @var \Broadway\EventHandling\EventBusInterface
   */
  protected $eventBus;

  /**
   * The execution delay.
   *
   * @var \ValueObjects\Number\Natural
   */
  protected $executionDelay;

  /**
   * The logger.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * EventBusForwardingConsumerFactoryFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.
   * @param \CultuurNet\Deserializer\DeserializerLocatorInterface $deserializer_locator
   *   The deserializer locator.
   * @param \Broadway\EventHandling\EventBusInterface $event_bus
   *   The event bus.
   */
  public function __construct(
    ConfigFactory $config,
    LoggerInterface $logger,
    DeserializerLocatorInterface $deserializer_locator,
    EventBusInterface $event_bus
  ) {

    $config = $config->get('culturefeed_udb3.settings');

    $this->executionDelay = Natural::fromNative($config->get('execution_delay'));
    $this->connectionConfig = $config->get('amqp');
    $this->logger = $logger;
    $this->deserializerLocator = $deserializer_locator;
    $this->eventBus = $event_bus;

  }

  /**
   * Get the event bus forwarding consumer factory.
   *
   * @return \CultuurNet\BroadwayAMQP\EventBusForwardingConsumerFactory
   *   The event bus forwarding consumer factory.
   */
  public function get() {

    return new EventBusForwardingConsumerFactory(
      $this->executionDelay,
      $this->connectionConfig,
      $this->logger,
      $this->deserializerLocator,
      $this->eventBus
    );

  }

}
