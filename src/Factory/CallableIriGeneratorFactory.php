<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Iri\CallableIriGenerator;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Url;

/**
 * Class CallableIriGeneratorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class CallableIriGeneratorFactory {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory;
   */
  protected $config;

  /**
   * The route name.
   *
   * @var string
   */
  protected $routeName;

  /**
   * The url config name (in culturefeed_udb3.settings).
   *
   * @var string
   */
  protected $urlConfigName;

  /**
   * CallableIriGeneratorFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   * @param string $route_name
   *   The route name.
   * @param string $url_config_name
   *   The url config name (in culturefeed_udb3.settings).
   */
  public function __construct(ConfigFactory $config, $route_name, $url_config_name = 'url') {
    $this->config = $config->get('culturefeed_udb3.settings');
    $this->routeName = $route_name;
    $this->urlConfigName = $url_config_name;
  }

  /**
   * Get the callable iri generator.
   *
   * @return \CultuurNet\UDB3\Iri\CallableIriGenerator|null
   *   The callable iri generator.
   */
  public function get() {

    $base_url = $this->config->get($this->urlConfigName);
    $route_name = $this->routeName;

    return new CallableIriGenerator(
      function ($cdbid) use ($route_name, $base_url) {
        if ($cdbid) {
          $url = Url::fromRoute($route_name, array('cdbid' => $cdbid), array('base_url' => $base_url));
          return $url->toString();
        }
      }
    );

  }

}
