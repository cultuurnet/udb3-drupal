<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Symfony\Proxy\CdbXmlProxy;
use Drupal\Core\Config\ConfigFactory;
use ValueObjects\String\String;
use ValueObjects\Web\Url;

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
   * The cdbxml redirect factory.
   *
   * @var \Drupal\culturefeed_udb3\Factory\CdbXmlRedirectFactory
   */
  protected $redirectFactory;

  /**
   * CdbXmlProxyFactory constructor.
   *
   * @param \Drupal\culturefeed_udb3\Factory\CdbXmlRedirectFactory $redirect_factory
   *   The redirect factory.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(CdbXmlRedirectFactory $redirect_factory, ConfigFactory $config) {

    $this->redirectFactory = $redirect_factory;
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

    $redirect_domain = Url::fromNative(
      $this->config->get('cdbxml_proxy.redirect_domain')
    );

    return new CdbXmlProxy(
      $accept,
      $redirect_domain,
      $this->redirectFactory
    );

  }

}
