<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\UDB2\EntryAPIFactory;

/**
 * The interface for creating an entry api factory.
 */
interface UDB2EntryApiFactoryInterface {

  /**
   * Returns entry api factory.
   *
   * @return EntryAPIFactory
   *   The entry api factory.
   */
  public function get();

}
