<?php

/**
 * @file
   * Contains Drupal\culturefeed_udb3\SavedSearches\SavedSearchesServiceFactory.
 */

namespace Drupal\culturefeed_udb3\SavedSearches;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\UDB2\Consumer;
use Drupal\Core\Config\ConfigFactory;
use CultuurNet\UDB3\SavedSearches\SavedSearchesServiceFactory;

/**
 * Class SavedSearchesFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class SavedSearchesServiceFactoryFactory {

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

  /**
   * @param ConfigFactory $config_factory
   * @param ConsumerCredentials $consumerCredentials
   */
  public function __construct(
    ConfigFactory $config_factory,
    ConsumerCredentials $consumerCredentials
  ) {
    $this->config = $config_factory->get('culturefeed.api');
    $this->consumerCredentials = $consumerCredentials;
  }

  /**
   * Get a factory for creating saved searches service factories
   *
   * @return \CultuurNet\UDB3\SavedSearches\SavedSearchesServiceFactory
   */
  public function get() {
    $config = $this->config;
    $api_location = $config->get('api_location');

    $consumer = new Consumer($api_location, $this->consumerCredentials);
    return new SavedSearchesServiceFactory($consumer);
  }

}
