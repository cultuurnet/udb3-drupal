<?php

namespace Drupal\culturefeed_udb3\Factory;

/**
 * The interface for creating an entry api factory.
 */
interface UDB2EntryApiFactoryInterface {

  /**
   * Returns entry api factory.
   *
   * @return \CultuurNet\UDB3\UDB2\EntryAPIFactory
   *   The entry api factory.
   */
  public function get();

}
