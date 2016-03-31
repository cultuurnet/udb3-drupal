<?php

namespace Drupal\culturefeed_udb3\Factory;

use Drupal\Core\Config\ConfigFactory;
use ValueObjects\Web\Domain;

/**
 * Class LocalDomainFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class LocalDomainFactory {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * LocalDomainFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(ConfigFactory $config) {
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * Return the domain.
   *
   * @return \ValueObjects\Web\Hostname|\ValueObjects\Web\IPAddress
   *   The domain.
   */
  public function get() {
    return Domain::specifyType(
      parse_url($this->config->get('url'))['host']
    );
  }

}
