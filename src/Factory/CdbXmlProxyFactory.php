<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Symfony\Proxy\CdbXmlProxy;
use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use ValueObjects\String\String;
use ValueObjects\Web\Hostname;
use ValueObjects\Web\PortNumber;

/**
 * Class CdbXmlProxyFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class CdbXmlProxyFactory {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * CdbXmlProxyFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(ConfigFactory $config) {
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * Get the cdbxml proxy.
   *
   * @return \CultuurNet\UDB3\Symfony\Proxy\CdbXmlProxy
   *   The cdbxml proxy.
   */
  public function get() {

    $accept = new String(
      $this->config->get('cdbxml_proxy.accept')
    );

    /* @var \ValueObjects\Web\Domain $redirect_domain */
    $redirect_domain = Hostname::fromNative(
      $this->config->get('cdbxml_proxy.redirect_domain')
    );

    $redirect_port = PortNumber::fromNative(
      $this->config->get('cdbxml_proxy.redirect_port')
    );

    return new CdbXmlProxy(
      $accept,
      $redirect_domain,
      $redirect_port,
      new DiactorosFactory(),
      new HttpFoundationFactory(),
      new Client()
    );

  }

}
