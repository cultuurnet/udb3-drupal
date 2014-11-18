<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\LoggerCommandBusFactory.
 */

namespace Drupal\culturefeed_udb3;

use Drupal\Core\Config\ConfigFactory;
use Monolog\Logger;
use Monolog\Handler\HipChatHandler;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;
use SocketIO\Emitter;
use Redis;
use Predis\Client;
use CultuurNet\UDB3\Monolog\SocketIOEmitterHandler;

class LoggerCommandBusFactory implements LoggerCommandBusFactoryInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory;
   */
  protected $config;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Constructs a command bus logger.
   *
   * @param ConfigFactory $config_factory
   *   The config factory.
   * @param LoggerInterface $logger
   *   The logger.
   */
  public function __construct(ConfigFactory $config_factory, LoggerInterface $logger) {
    $this->config = $config_factory->get('culturefeed_udb3.settings');
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    $config = $this->config;
    $logger = new Logger('command_bus');

    $handler = $this->logger;

    if ($config->get('log.command_bus.hipchat')) {

      $handler = new HipChatHandler(
        $config->get('log.command_bus.hipchat_token'),
        $config->get('log.command_bus.hipchat_room')
      );

    }

    if ($config->get('log.command_bus.file')) {

      $path = $config->get('log.command_bus.file_path');
      $handler = new StreamHandler('public://' . $path);

    }

    if ($config->get('log.command_bus.socketioemitter')) {

      $redis_host = ($config->get('log.command_bus.socketioemitter_redis_host')) ? $config->get('log.command_bus.socketioemitter_redis_host') : '127.0.0.1';
      $redis_port = ($config->get('log.command_bus.socketioemitter_redis_port')) ? $config->get('log.command_bus.socketioemitter_redis_port') : '6379';

      if (extension_loaded('redis')) {

        $redis = new Redis();
        $redis->connect($redis_host, $redis_port);

      }
      else {

        $redis = new Client(array(
          'host' => $redis_host,
          'port' => $redis_port,
        ));
        $redis->connect();

      }

      $emitter = new Emitter($redis);

      $handler = new SocketIOEmitterHandler($emitter);

    }

    $handler->setLevel($config->get('log.command_bus.level'));
    $logger->pushHandler($handler);

    return $logger;

  }


}
