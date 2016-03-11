<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\EventCdbXmlFromEntryAPIFactory.
 */

namespace Drupal\culturefeed_udb3;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\UDB2\EventCdbXmlFromEntryAPI;
use Drupal\Core\Config\ConfigFactory;
use ValueObjects\String\String;

/**
 * Class UDB2EntryApiFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class EventCdbXmlFromEntryAPIFactory implements EventCdbXmlFromEntryAPIFactoryInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The credentials.
   *
   * @var \CultuurNet\Auth\ConsumerCredentials
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
    $this->configFactory = $config_factory;
    $this->credentials = $credentials;
  }

  /**
   * {@inheritdoc}
   */
  public function get() {
    $config = $this->configFactory->get('culturefeed.api');
    $api_location = $config->get('api_location');
    $entry_api_path = $config->get('entry_api_path');
    $base_url = $api_location . $entry_api_path;

    $config = $this->configFactory->get('culturefeed_udb3.settings');
    $user_id = new String($config->get('impersonation_user_id'));

    return new EventCdbXmlFromEntryAPI(
      $base_url,
      $this->credentials,
      $user_id,
      // @todo Move the cdbxml version to configuration file. Use the same
      // setting when instantiating the ImprovedEntryApiFactory.
      'http://www.cultuurdatabank.com/XMLSchema/CdbXSD/3.3/FINAL'
    );
  }

}
