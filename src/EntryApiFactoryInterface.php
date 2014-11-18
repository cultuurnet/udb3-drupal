<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\EntryApiFactoryInterface.
 */

namespace Drupal\culturefeed_udb3;

use CultuurNet\UDB3\UDB2\EntryAPIFactory;

/**
 * The interface for creating an entry api factory.
 */
interface EntryApiFactoryInterface {

  /**
   * Returns entry api factory.
   *
   * @return EntryAPIFactory
   *   The entry api factory.
   */
  public function get();

}