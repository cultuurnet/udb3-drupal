<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\PlacePermissionProjectorFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Place\ReadModel\Permission\Projector;
use CultuurNet\UDB3\UiTID\CdbXmlCreatedByToUserIdResolver;
use Drupal\culturefeed_udb3\Repository\PlacePermissionRepository;
use CultuurNet\UDB3\UiTID\UsersInterface;

/**
 * Class PlacePermissionProjectorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class PlacePermissionProjectorFactory {

  /**
   * The place permission repository.
   *
   * @var \Drupal\culturefeed_udb3\Repository\PlacePermissionRepository
   */
  protected $placePermissionRepository;

  /**
   * The uitid users.
   *
   * @var \CultuurNet\UDB3\UiTID\UsersInterface
   */
  protected $uitidUsers;

  /**
   * EventPermissionProjectorFactory constructor.
   *
   * @param \Drupal\culturefeed_udb3\Repository\PlacePermissionRepository $place_permission_repository
   *   The event permission repository.
   * @param \CultuurNet\UDB3\UiTID\UsersInterface $uitid_users
   *   The uitid users.
   */
  public function __construct(PlacePermissionRepository $place_permission_repository, UsersInterface $uitid_users) {
    $this->placePermissionRepository = $place_permission_repository;
    $this->uitidUsers = $uitid_users;
  }

  /**
   * Get the place permission projector.
   *
   * @return \CultuurNet\UDB3\Place\ReadModel\Permission\Projector
   *   The place permission projector.
   */
  public function get() {
    $created_by_to_user_id = new CdbXmlCreatedByToUserIdResolver($this->uitidUsers);
    return new Projector($this->placePermissionRepository, $created_by_to_user_id);
  }

}
