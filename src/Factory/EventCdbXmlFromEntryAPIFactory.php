<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\UDB3\UDB2\EventCdbXmlFromEntryAPI;
use Drupal\Core\Config\ConfigFactory;
use ValueObjects\String\String;

/**
 * Class EventCdbXmlFromEntryAPIFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class EventCdbXmlFromEntryAPIFactory implements EventCdbXmlFromEntryAPIFactoryInterface {

  /**
   * The cdb xml namespace uri.
   *
   * @var string
   */
  protected $cdbXmlNamespaceUri;

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
   * @param string $cdb_xml_namespace_uri
   *   The cdb xml namespace uri.
   */
  public function __construct(ConfigFactory $config_factory, ConsumerCredentials $credentials, $cdb_xml_namespace_uri) {
    $this->configFactory = $config_factory;
    $this->credentials = $credentials;
    $this->cdbXmlNamespaceUri = $cdb_xml_namespace_uri;
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
      $this->cdbXmlNamespaceUri
    );
  }

}
