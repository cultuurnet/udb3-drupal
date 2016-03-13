<?php

namespace Drupal\culturefeed_udb3\Factory;

/**
 * The interface for creating an entry api factory.
 */
interface LoggerCommandBusFactoryInterface {

  /**
   * Returns command bus logger.
   *
   * @return \Monolog\Logger
   *   The logger.
   */
  public function get();

}
