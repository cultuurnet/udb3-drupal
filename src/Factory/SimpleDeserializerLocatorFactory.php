<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\Deserializer\SimpleDeserializerLocator;
use CultuurNet\UDB2DomainEvents\ActorCreatedJSONDeserializer;
use CultuurNet\UDB2DomainEvents\ActorUpdatedJSONDeserializer;
use CultuurNet\UDB2DomainEvents\EventCreatedJSONDeserializer;
use CultuurNet\UDB2DomainEvents\EventUpdatedJSONDeserializer;
use ValueObjects\String\String;

/**
 * Class SimpleDeserializerLocatorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class SimpleDeserializerLocatorFactory {

  /**
   * Get the simple deserializer locator.
   *
   * @return \CultuurNet\Deserializer\SimpleDeserializerLocator
   *   The simple deserializer locator.
   */
  public function get() {

    $deserializer_locator = new SimpleDeserializerLocator();
    $deserializer_locator->registerDeserializer(
      new String(
        'application/vnd.cultuurnet.udb2-events.actor-created+json'
      ),
      new ActorCreatedJSONDeserializer()
    );
    $deserializer_locator->registerDeserializer(
      new String(
        'application/vnd.cultuurnet.udb2-events.actor-updated+json'
      ),
      new ActorUpdatedJSONDeserializer()
    );
    $deserializer_locator->registerDeserializer(
      new String(
        'application/vnd.cultuurnet.udb2-events.event-created+json'
      ),
      new EventCreatedJSONDeserializer()
    );
    $deserializer_locator->registerDeserializer(
      new String(
        'application/vnd.cultuurnet.udb2-events.event-updated+json'
      ),
      new EventUpdatedJSONDeserializer()
    );
    return $deserializer_locator;

  }

}
