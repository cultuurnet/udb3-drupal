<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\Factory\PlaceRepositoryFactoryInterface.
 */

namespace Drupal\culturefeed_udb3\Factory;

/**
 * The interface for creating a place repository factory.
 */
interface PlaceRepositoryFactoryInterface {

  /**
   * Returns place repository factory.
   *
   * @return \CultuurNet\UDB3\UDB2\Place\PlaceRepository
   *   The place repository.
   */
  public function get();

}
