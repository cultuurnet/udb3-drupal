<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\Udb2EventCdbxmlFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\UDB2\Event\SpecificationDecoratedEventCdbXml;
use CultuurNet\UDB3\UDB2\EventCdbXmlFromEntryAPI;
use CultuurNet\UDB3\UDB2\LabeledAsUDB3Place;
use CultuurNet\UDB3\Cdb\Event\Not;

/**
 * Class Udb2EventCdbxmlFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class Udb2EventCdbxmlFactory {

  /**
   * The event cdbxml provider.
   *
   * @var \CultuurNet\UDB3\UDB2\EventCdbXmlFromEntryAPI
   */
  private $eventCdbxmlProvider;

  /**
   * Udb2EventCdbxmlFactory constructor.
   *
   * @param \CultuurNet\UDB3\UDB2\EventCdbXmlFromEntryAPI $event_cdbxml_provider
   *   The event cdbxml provider.
   */
  public function __construct(EventCdbXmlFromEntryAPI $event_cdbxml_provider) {
    $this->eventCdbxmlProvider = $event_cdbxml_provider;
  }

  /**
   * Get the decorated event cdbxml.
   *
   * @return \CultuurNet\UDB3\UDB2\Event\SpecificationDecoratedEventCdbXml
   *   The decorated event cdbxml.
   */
  public function get() {
    $labeled_as_udb3_place = new LabeledAsUDB3Place();

    return new SpecificationDecoratedEventCdbXml(
      $this->eventCdbxmlProvider,
      new Not($labeled_as_udb3_place)
    );
  }

}
