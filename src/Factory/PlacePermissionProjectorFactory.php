<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionRepositoryInterface;
use CultuurNet\UDB3\Place\ReadModel\Permission\Projector;
use CultuurNet\UDB3\UiTID\CdbXmlCreatedByToUserIdResolver;

/**
 * Class PlacePermissionProjectorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class PlacePermissionProjectorFactory extends OfferPermissionProjectorFactory {

  /**
   * {@inheritdoc}
   */
  protected function instantiateProjector(
      PermissionRepositoryInterface $permission_repository,
      CdbXmlCreatedByToUserIdResolver $created_by_to_user_id_resolver
  ) {
    return new Projector($permission_repository, $created_by_to_user_id_resolver);
  }

}
