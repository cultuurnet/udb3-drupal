<?php

namespace Drupal\culturefeed_udb3;

use CultureFeed_User;
use CultuurNet\UDB3\Offer\SecurityInterface;
use CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionRepositoryInterface;
use ValueObjects\String\String;

/**
 * Class EventSecurity.
 *
 * @package Drupal\culturefeed_udb3\Security
 */
class Security implements SecurityInterface {

  /**
   * The permission repository.
   *
   * @var \CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionQueryInterface
   */
  protected $permissionRepository;

  /**
   * The culturefeed user.
   *
   * @var \CultureFeed_User
   */
  protected $user;

  /**
   * EventSecurity constructor.
   *
   * @param \CultuurNet\UDB3\Offer\ReadModel\Permission\PermissionRepositoryInterface $permission_repository
   *   The permission repository.
   * @param \Culturefeed_User $user
   *   The culturefeed user.
   */
  public function __construct(PermissionRepositoryInterface $permission_repository, Culturefeed_User $user) {
    $this->permissionRepository = $permission_repository;
    $this->user = $user;
  }

  /**
   * Returns the edit access for an offer.
   *
   * @param \ValueObjects\String\String $offer_id
   *   The offer id.
   *
   * @return bool
   *   Edit access.
   */
  public function currentUitIdUserCanEditOffer(String $offer_id) {
    $user_id = new String($this->user->id);
    $editable_events = $this->permissionRepository->getEditableOffers($user_id);
    return in_array($offer_id, $editable_events);
  }

  /**
   * {@inheritdoc}
   */
  public function allowsUpdates(String $offer_id) {
    return $this->currentUitIdUserCanEditOffer($offer_id);
  }

  /**
   * {@inheritdoc}
   */
  public function allowsUpdateWithCdbXml(String $offer_id) {
    return $this->currentUitIdUserCanEditOffer($offer_id);
  }

}
