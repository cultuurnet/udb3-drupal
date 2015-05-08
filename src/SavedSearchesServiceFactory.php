<?php

/**
 * @file
   * Contains Drupal\culturefeed_udb3\SavedSearchesServiceFactory.
 */

namespace Drupal\culturefeed_udb3;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\UDB2\Consumer;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class SavedSearchesFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class SavedSearchesServiceFactory {

  /**
   * The config factory.
   *
   * @var ConfigFactory
   */
  protected $config;

  /**
   * The credentials.
   *
   * @var ConsumerCredentials
   */
  protected $consumerCredentials;

  public function __construct(
    ConfigFactory $config_factory,
    ConsumerCredentials $consumerCredentials
  ) {
    $this->config = $config_factory->get('culturefeed.api');
    $this->consumerCredentials = $consumerCredentials;
  }

  public function get() {
    $config = $this->config;
    $api_location = $config->get('api_location');

    $consumer = new Consumer($api_location, $this->consumerCredentials);
    return new \CultuurNet\UDB3\SavedSearches\SavedSearchesServiceFactory($consumer);
  }

}
