<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Factory\EventRepositoryFactoryInterface.
 */

namespace Drupal\culturefeed_udb3\Factory;

/**
 * The interface for creating an event repository factory.
 */
interface EventRepositoryFactoryInterface {

  /**
   * Returns event repository factory.
   *
   * @return \CultuurNet\UDB3\UDB2\EventRepository
   *   The event repository.
   */
  public function get();

}
