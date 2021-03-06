<?php

namespace Drupal\culturefeed_udb3\Factory;

use Drupal\Core\Config\ConfigFactory;
use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\SearchAPI2\DefaultSearchService;

/**
 * Class SearchApi2Factory.
 *
 * @package Drupal\culturefeed_udb3
 */
class SearchApi2Factory {

  /**
   * The search api 2 config.
   *
   * @var \Drupal\Core\Config\Config;
   */
  protected $config;

  /**
   * Constructs the search api.
   *
   * @param ConfigFactory $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactory $config_factory) {

    $this->config = $config_factory->get('culturefeed_search.api');
  }

  /**
   * Creates the default search service.
   *
   * @return DefaultSearchService
   *   The default search service.
   */
  public function create() {
    $consumer_credentials = new ConsumerCredentials(
      $this->config->get('application_key'),
      $this->config->get('shared_secret')
    );

    return new DefaultSearchService(
      $this->config->get('api_location'),
      $consumer_credentials
    );
  }

}
