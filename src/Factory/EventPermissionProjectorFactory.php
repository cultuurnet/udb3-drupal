<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\EventPermissionProjectorFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Event\ReadModel\Permission\Projector;
use CultuurNet\UDB3\UiTID\CdbXmlCreatedByToUserIdResolver;
use Drupal\culturefeed_udb3\Repository\EventPermissionRepository;
use CultuurNet\UDB3\UiTID\UsersInterface;

/**
 * Class EventPermissionProjectorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class EventPermissionProjectorFactory {

  /**
   * The event permission repository.
   *
   * @var \Drupal\culturefeed_udb3\Repository\EventPermissionRepository
   */
  protected $eventPermissionRepository;

  /**
   * The uitid users.
   *
   * @var \CultuurNet\UDB3\UiTID\UsersInterface
   */
  protected $uitidUsers;

  /**
   * EventPermissionProjectorFactory constructor.
   *
   * @param \Drupal\culturefeed_udb3\Repository\EventPermissionRepository $event_permission_repository
   *   The event permission repository.
   * @param \CultuurNet\UDB3\UiTID\UsersInterface $uitid_users
   *   The uitid users.
   */
  public function __construct(EventPermissionRepository $event_permission_repository, UsersInterface $uitid_users) {
    $this->eventPermissionRepository = $event_permission_repository;
    $this->uitidUsers = $uitid_users;
  }

  /**
   * Get the event permission projector.
   *
   * @return \CultuurNet\UDB3\Event\ReadModel\Permission\Projector
   *   The event permission projector.
   */
  public function get() {
    $created_by_to_user_id = new CdbXmlCreatedByToUserIdResolver($this->uitidUsers);
    return new Projector($this->eventPermissionRepository, $created_by_to_user_id);
  }

}
