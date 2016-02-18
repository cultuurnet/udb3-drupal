<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\OfferPermissionProjectorFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use Broadway\EventHandling\EventListenerInterface;
use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionRepositoryInterface;
use CultuurNet\UDB3\UiTID\CdbXmlCreatedByToUserIdResolver;
use CultuurNet\UDB3\UiTID\UsersInterface;

/**
 * Class OfferPermissionProjectorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
abstract class OfferPermissionProjectorFactory {

    /**
     * The permission repository.
     *
     * @var PermissionRepositoryInterface
     */
    protected $permissionRepository;

    /**
     * The uitid users.
     *
     * @var \CultuurNet\UDB3\UiTID\UsersInterface
     */
    protected $uitidUsers;

    /**
     * OfferPermissionProjectorFactory constructor.
     *
     * @param PermissionRepositoryInterface $permission_repository
     *   The permission repository.
     *
     * @param \CultuurNet\UDB3\UiTID\UsersInterface $uitid_users
     *   The uitid users.
     */
    public function __construct(PermissionRepositoryInterface $permission_repository, UsersInterface $uitid_users) {
        $this->permissionRepository = $permission_repository;
        $this->uitidUsers = $uitid_users;
    }

    /**
     * Get the permission projector.
     *
     * @return EventListenerInterface
     *   The projector
     */
    public function get() {
        $created_by_to_user_id = new CdbXmlCreatedByToUserIdResolver($this->uitidUsers);
        return $this->instantiateProjector($this->permissionRepository, $created_by_to_user_id);
    }

    /**
     * Instantiates the actual Project class.
     *
     * @param PermissionRepositoryInterface $permission_repository
     *   The permission repository.
     *
     * @param CdbXmlCreatedByToUserIdResolver $created_by_to_user_id_resolver
     *   The created by to user id resolver.
     *
     * @return EventListenerInterface
     *   The projector
     */
    abstract protected function instantiateProjector(
        PermissionRepositoryInterface $permission_repository,
        CdbXmlCreatedByToUserIdResolver $created_by_to_user_id_resolver
    );

}
