<?php

namespace Drupal\culturefeed_udb3\Factory;

/**
 * The interface for creating events cdbxml from entry api factory.
 */
interface EventCdbXmlFromEntryAPIFactoryInterface {

  /**
   * Returns events cdbxml from entry api.
   *
   * @return \CultuurNet\UDB3\UDB2\EventCdbXmlFromEntryAPI
   *   The events cdbxml from entry api.
   */
  public function get();

}
