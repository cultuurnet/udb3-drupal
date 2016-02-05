<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\SavedSearchesFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\Auth\ConsumerCredentials;
use Drupal\Core\Config\ConfigFactory;
use Drupal\culturefeed\UserCredentials;

/**
 * Class SavedSearchesFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class SavedSearchesFactory {

  /**
   * The consumer credentials.
   *
   * @var \CultuurNet\Auth\ConsumerCredentials
   */
  private $consumerCredentials;

  /**
   * The user credentials.
   *
   * @var \Drupal\culturefeed\UserCredentials
   */
  private $userCredentials;

  /**
   * SavedSearchesFactory constructor.
   *
   * @param \CultuurNet\Auth\ConsumerCredentials $consumer_credentials
   *   The consumer credentials.
   * @param \Drupal\culturefeed\UserCredentials $user_credentials
   *   The user credentials.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   */
  public function __construct(ConsumerCredentials $consumer_credentials, UserCredentials $user_credentials, ConfigFactory $config_factory) {
    $this->consumerCredentials = $consumer_credentials;
    $this->userCredentials = $user_credentials;
    $this->config = $config_factory->get('culturefeed.api');
  }

  /**
   * Get the saved searches service.
   *
   * @return \CultuurNet\UDB3\SimpleEventBus
   *   The saved searches service.
   */
  public function get() {

    $oauth_client = new \CultureFeed_DefaultOAuthClient(
      $this->consumerCredentials->getKey(),
      $this->consumerCredentials->getSecret(),
      $this->userCredentials->getToken(),
      $this->userCredentials->getSecret()
    );

    $oauth_client->setEndpoint($this->config->get('api_location'));

    $culture_feed = new \CultureFeed($oauth_client);
    return new \CultureFeed_SavedSearches_Default($culture_feed);
  }

}
