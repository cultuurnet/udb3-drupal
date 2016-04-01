<?php

namespace Drupal\culturefeed_udb3\Factory;

use ValueObjects\Web\Domain;

/**
 * Class Udb2DomainFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class Udb2DomainFactory {

  /**
   * The udb2 domain.
   *
   * @var string
   */
  protected $domain;

  /**
   * LocalDomainFactory constructor.
   *
   * @param string $domain
   *   The udb2 domain.
   */
  public function __construct($domain) {
    $this->domain = $domain;
  }

  /**
   * Return the domain.
   *
   * @return \ValueObjects\Web\Hostname|\ValueObjects\Web\IPAddress
   *   The domain.
   */
  public function get() {
    return Domain::specifyType($this->domain);
  }

}
