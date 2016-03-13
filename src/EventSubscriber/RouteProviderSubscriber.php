<?php

namespace Drupal\culturefeed_udb3\EventSubscriber;

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Route;

/**
 * Ensures that routes can be provided by entity types.
 */
class RouteProviderSubscriber implements EventSubscriberInterface {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  private $moduleHandler;

  /**
   * RouteProviderSubscriber constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->moduleHandler = $module_handler;
  }

  /**
   * Provides routes on route rebuild time.
   *
   * @param \Drupal\Core\Routing\RouteBuildEvent $event
   *   The route build event.
   */
  public function onDynamicRouteEvent(RouteBuildEvent $event) {
    $route_collection = $event->getRouteCollection();

    $routes = array();
    $module = $this->moduleHandler->getModule('culturefeed_udb3');
    $path = $module->getPath() . '/routing';
    $files = file_scan_directory($path, '/.*/');

    foreach ($files as $file) {
      $routes += Yaml::decode(file_get_contents($file->uri));
    }

    foreach ($routes as $route_name => $route_info) {

      // Copied from Drupal\Core\Routing\RouteBuilder::rebuild().
      $route_info += array(
        'defaults' => array(),
        'requirements' => array(),
        'options' => array(),
        'host' => NULL,
        'schemes' => array(),
        'methods' => array(),
        'condition' => '',
      );

      $route = new Route($route_info['path'], $route_info['defaults'], $route_info['requirements'], $route_info['options'], $route_info['host'], $route_info['schemes'], $route_info['methods'], $route_info['condition']);

      // Don't override existing routes.
      if (!$route_collection->get($route_name)) {
        $route_collection->add($route_name, $route);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // See Drupal\Core\Routing\RouteBuilder:
    // DYNAMIC is supposed to be used to add new routes based upon all the
    // static defined ones.
    $events[RoutingEvents::DYNAMIC][] = ['onDynamicRouteEvent'];
    return $events;
  }

}
