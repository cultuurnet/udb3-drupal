<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\BroadwayAMQP\EventBusForwardingConsumerFactory;
use Drupal\Core\Config\ConfigFactory;
use ValueObjects\String\String;

/**
 * Class AMQPEventBusForwardingConsumerFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class AMQPEventBusForwardingConsumerFactory {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * The event bus forwarding consumer factory.
   *
   * @var \CultuurNet\BroadwayAMQP\EventBusForwardingConsumerFactory
   */
  protected $eventBusForwardingConsumerFactory;

  /**
   * AMQPStreamConnectionFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   * @param \CultuurNet\BroadwayAMQP\EventBusForwardingConsumerFactory $event_bus_forwarding_consumer_factory
   *   The event bus forwarding consumer factory.
   */
  public function __construct(ConfigFactory $config, EventBusForwardingConsumerFactory $event_bus_forwarding_consumer_factory) {
    $this->config = $config->get('culturefeed_udb3');
    $this->eventBusForwardingConsumerFactory = $event_bus_forwarding_consumer_factory;
  }

  /**
   * Get the AMQP stream connection.
   *
   * @return \PhpAmqpLib\Connection\AMQPStreamConnection
   *   The AMQP stream connection.
   */
  public function get() {

    $consumer_config = $this->config->get('consumers.udb2');
    $exchange = new String($consumer_config['exchange']);
    $queue = new String($consumer_config['queue']);

    $eventBusForwardingConsumer = $this->eventBusForwardingConsumerFactory->create($exchange, $queue);

    return $eventBusForwardingConsumer->getConnection();

  }

}
