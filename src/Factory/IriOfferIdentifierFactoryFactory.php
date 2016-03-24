<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Offer\IriOfferIdentifierFactory;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class IriOfferIdentifierFactoryFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class IriOfferIdentifierFactoryFactory {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * IriOfferIdentifierFactoryFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(ConfigFactory $config) {
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * Get the iri offer identifier factory.
   *
   * @return \CultuurNet\UDB3\Offer\IriOfferIdentifierFactory
   *   The iri offer identifier factory.
   */
  public function get() {

    $offer_url_regex = $this->config->get('offer_url_regex');
    return new IriOfferIdentifierFactory($offer_url_regex);

  }

}
