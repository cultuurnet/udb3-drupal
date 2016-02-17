<?php

namespace Drupal\culturefeed_udb3\Factory;

use Drupal\Core\Routing\AccessAwareRouter;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class SymfonyUrlGeneratorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class SymfonyUrlGeneratorFactory {

  /**
   * The drupal path.
   *
   * Will be converted to a route collection that matches the external route
   * name.
   *
   * @var string
   */
  protected $drupalPath;

  /**
   * The external route name.
   *
   * Will be mapped to a route collection that contains the drupal route.
   */
  protected $externalRouteName;

  /**
   * The router.
   *
   * @var \Drupal\Core\Routing\AccessAwareRouter
   */
  protected $router;

  /**
   * SymfonyUrlGeneratorFactory constructor.
   *
   * @param \Drupal\Core\Routing\AccessAwareRouter $router
   *   The router.
   * @param string $drupal_path
   *   The drupal path.
   * @param string $external_route_name
   *   The external route name.
   */
  public function __construct(
    AccessAwareRouter $router,
    $drupal_path,
    $external_route_name
  ) {
    $this->router = $router;
    $this->drupalPath = $drupal_path;
    $this->externalRouteName = $external_route_name;
  }

  /**
   * Gt the symfony url generator.
   *
   * @return \Symfony\Component\Routing\Generator\UrlGenerator
   *   The symfony url generator.
   */
  public function get() {

    $route_collection = new RouteCollection();
    try {
      $route_info = $this->router->match($this->drupalPath);
      $route_collection->add($this->externalRouteName, $route_info['_route_object']);
    }
    catch (ResourceNotFoundException $e) {
      // Drupal has no routes yet.
    }
    $context = $this->router->getContext();
    return new UrlGenerator($route_collection, $context);

  }

}
