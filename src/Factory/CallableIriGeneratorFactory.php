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
   * CallableIriGeneratorFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   * @param string $route_name
   *   The route name.
   */
  public function __construct(ConfigFactory $config, $route_name) {
    $this->config = $config->get('culturefeed_udb3.settings');
    $this->routeName = $route_name;
  }

  /**
   * Get the callable iri generator.
   *
   * @return \CultuurNet\UDB3\Iri\CallableIriGenerator
   *   The callable iri generator.
   */
  public function get() {

    $base_url = $this->config->get('url');
    $route_name = $this->routeName;

    return new CallableIriGenerator(
      function ($cdbid) use ($route_name, $base_url) {
        $url = Url::fromRoute($route_name, array('cdbid' => $cdbid), array('base_url' => $base_url));
        return $url->toString();
      }
    );

  }

}
