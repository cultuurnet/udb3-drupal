<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UDB3\SavedSearches\SavedSearchesServiceFactoryInterface;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class SavedSearchesServiceFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class SavedSearchesServiceFactory implements SavedSearchesServiceFactoryInterface {

  /**
   * The config factory.
   *
   * @var ConfigFactory
   */
  protected $config;

  /**
   * The consumer credentials.
   *
   * @var \CultuurNet\Auth\ConsumerCredentials
   */
  private $consumerCredentials;

  /**
   * SavedSearchesFactory constructor.
   *
   * @param \CultuurNet\Auth\ConsumerCredentials $consumer_credentials
   *   The consumer credentials.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   */
  public function __construct(ConsumerCredentials $consumer_credentials, ConfigFactory $config_factory) {
    $this->consumerCredentials = $consumer_credentials;
    $this->config = $config_factory->get('culturefeed.api');
  }

  /**
   * {@inheritdoc}
   */
  public function withTokenCredentials(TokenCredentials $token_credentials) {

    $oauth_client = new \CultureFeed_DefaultOAuthClient(
      $this->consumerCredentials->getKey(),
      $this->consumerCredentials->getSecret(),
      $token_credentials->getToken(),
      $token_credentials->getSecret()
    );

    $oauth_client->setEndpoint($this->config->get('api_location'));

    $culture_feed = new \CultureFeed($oauth_client);
    return new \CultureFeed_SavedSearches_Default($culture_feed);
  }

}
