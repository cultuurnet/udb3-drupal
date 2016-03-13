<?php

namespace Drupal\culturefeed_udb3\Factory;

/**
 * The interface for creating an organizer repository factory.
 */
interface OrganizerRepositoryFactoryInterface {

  /**
   * Returns organizer repository factory.
   *
   * @return \CultuurNet\UDB3\UDB2\Organizer\OrganizerRepository
   *   The organizer repository.
   */
  public function get();

}
