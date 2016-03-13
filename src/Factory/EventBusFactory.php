<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\SimpleEventBus;
use Broadway\EventHandling\EventBusInterface;

/**
 * Class EventBusFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class EventBusFactory {

  /**
   * Get the event bus.
   *
   * @param array $subscribers
   *   The subscribers.
   *
   * @return \CultuurNet\UDB3\SimpleEventBus
   *   The event bus.
   */
  public function get(array $subscribers) {
    $bus = new SimpleEventBus();

    $bus->beforeFirstPublication(function (EventBusInterface $event_bus) use ($subscribers) {
      foreach ($subscribers as $subscriber) {
        $event_bus->subscribe(\Drupal::service($subscriber));
      }
    });

    return $bus;
  }

}
