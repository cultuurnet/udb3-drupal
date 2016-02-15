<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\SymfonyUrlGeneratorFactory/
 */

namespace Drupal\culturefeed_udb3\Factory;

use Drupal\Core\Routing\AccessAwareRouter;
use Symfony\Component\Routing\Generator\UrlGenerator;

/**
 * Class SymfonyUrlGeneratorFactory
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class SymfonyUrlGeneratorFactory {

  /**
   * The router
   *
   * @var \Drupal\Core\Routing\AccessAwareRouter
   */
  protected $router;

  /**
   * SymfonyUrlGeneratorFactory constructor.
   *
   * @param \Drupal\Core\Routing\AccessAwareRouter $router
   *   The router.
   */
  public function __construct(AccessAwareRouter $router) {
    $this->router = $router;
  }

  /**
   * Gt the symfony url generator.
   *
   * @return \Symfony\Component\Routing\Generator\UrlGenerator
   *   The symfony url generator.
   */
  public function get() {

    $route_collection = $this->router->getRouteCollection();
    $context = $this->router->getContext();
    return new UrlGenerator($route_collection, $context);

  }

}