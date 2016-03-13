<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\UDB2\EventCdbXmlFromEntryAPI;

/**
 * The interface for creating events cdbxml from entry api factory.
 */
interface EventCdbXmlFromEntryAPIFactoryInterface {

  /**
   * Returns events cdbxml from entry api.
   *
   * @return EventCdbXmlFromEntryAPI
   *   The events cdbxml from entry api.
   */
  public function get();

}
