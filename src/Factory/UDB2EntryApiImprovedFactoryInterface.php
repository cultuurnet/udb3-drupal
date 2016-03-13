<?php

namespace Drupal\culturefeed_udb3\Factory;

/**
 * The interface for creating an improved entry api factory.
 */
interface UDB2EntryApiImprovedFactoryInterface {

  /**
   * Returns entry api factory.
   *
   * @return \CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory
   *   The improved entry api factory.
   */
  public function get();

}
