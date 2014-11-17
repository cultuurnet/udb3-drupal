<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\EntryApiFactory.
 */

namespace Drupal\culturefeed_udb3;

use Drupal\Core\Config\ConfigFactory;
use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\UDB2\Consumer;

class EntryApiFactory {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory;
   */
  protected $config;

  /**
   * The credentials.
   *
   * @var \CultuurNet\Auth\ConsumerCredentials;
   */
  protected $credentials;

  /**
   * Constructs an entry api factory.
   *
   * @param ConfigFactory $config_factory
   *   The config factory.
   * @param ConsumerCredentials $credentials
   *   The credentials.
   */
  public function __construct(ConfigFactory $config_factory, ConsumerCredentials $credentials) {
    $this->config = $config_factory->get('culturefeed.api');
    $this->credentials = $credentials;
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    $config = $this->config;
    $base_url = $config->get('culturefeed.api')->get('api_location');
    $consumer = new Consumer($base_url, $this->credentials);
    return new \CultuurNet\UDB3\UDB2\EntryAPIFactory($consumer);
  }


}
