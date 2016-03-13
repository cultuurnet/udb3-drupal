<?php

namespace Drupal\culturefeed_udb3\Access;

use CultureFeed_User;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UDB3\UDB2\EntryAPIImprovedFactory;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\culturefeed\UserCredentials;
use Drupal\culturefeed_udb3\Factory\UDB2EntryApiImprovedFactory;
use Exception;

/**
 * Access class to check if user has edit permissions.
 */
class EditAccess implements AccessInterface {

  /**
   * The culturefeed user.
   *
   * @var \CultureFeed_User;
   */
  private $user;

  /**
   * The user credentials.
   *
   * @var UserCredentials
   */
  private $credentials;

  /**
   * The improved entry api factory.
   *
   * @var EntryAPIImprovedFactory
   */
  private $entryApiImprovedFactory;

  /**
   * Constructs the user access object.
   *
   * @param CultureFeed_User $user
   *   The culturefeed user.
   * @param UserCredentials $credentials
   *   The user credentials.
   * @param UDB2EntryApiImprovedFactory $entryApiImprovedFactory
   *   The improved entry api factory.
   */
  public function __construct(CultureFeed_User $user, UserCredentials $credentials, UDB2EntryApiImprovedFactory $entryApiImprovedFactory) {
    $this->user = $user;
    $this->credentials = $credentials;
    $this->entryApiImprovedFactory = $entryApiImprovedFactory;
  }

  /**
   * {@inheritdoc}
   */
  public function access($id) {

    $tokenCredentials = new TokenCredentials($this->credentials->getToken(), $this->credentials->getSecret());
    $entryApi = $this->entryApiImprovedFactory->get()->withTokenCredentials($tokenCredentials);

    $has_permission = FALSE;
    try {
      $result = $entryApi->checkPermission($this->user->id, $this->user->mbox, array($id));
      if (!empty($result->event)) {
        $has_permission = (string) $result->event->editable == 'true';
      }

    }
    catch (Exception $e) {
      watchdog_exception('udb3', $e);
    }

    return AccessResult::allowedIf($has_permission);

  }

}
