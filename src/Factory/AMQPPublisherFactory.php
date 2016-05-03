<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\BroadwayAMQP\AMQPPublisher;
use CultuurNet\BroadwayAMQP\ContentTypeLookup;
use CultuurNet\BroadwayAMQP\DomainMessage\AnyOf;
use CultuurNet\BroadwayAMQP\DomainMessage\PayloadIsInstanceOf;
use CultuurNet\BroadwayAMQP\DomainMessage\SpecificationCollection;
use CultuurNet\BroadwayAMQP\Message\EntireDomainMessageBodyFactory;
use CultuurNet\UDB3\Event\Events\ContentTypes as EventContentTypes;
use CultuurNet\UDB3\Organizer\Events\ContentTypes as OrganizerContentTypes;
use CultuurNet\UDB3\Place\Events\ContentTypes as PlaceContentTypes;
use Drupal\Core\Config\ConfigFactory;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Class AMQPPublisherFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class AMQPPublisherFactory {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * AMQPPublisherFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(ConfigFactory $config) {
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * Get the AMQP publisher.
   *
   * @return \CultuurNet\BroadwayAMQP\AMQPPublisher
   *   The AMQP publisher.
   */
  public function get() {

    $amqp_config = $this->config->get('amqp');
    $connection = new AMQPStreamConnection(
      $amqp_config['host'],
      $amqp_config['port'],
      $amqp_config['user'],
      $amqp_config['password'],
      $amqp_config['vhost']
    );
    $exchange = $amqp_config['publish']['udb3']['exchange'];

    $channel = $connection->channel();

    $map = EventContentTypes::map() + OrganizerContentTypes::map() + PlaceContentTypes::map();

    $classes = (new SpecificationCollection());
    foreach (array_keys($map) as $className) {
      $classes = $classes->with(
        new PayloadIsInstanceOf($className)
      );
    }

    $specification = new AnyOf($classes);

    $contentTypeLookup = new ContentTypeLookup($map);

    $publisher = new AMQPPublisher(
      $channel,
      $exchange,
      $specification,
      $contentTypeLookup,
      new EntireDomainMessageBodyFactory()
    );

    return $publisher;

  }

}
