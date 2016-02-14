<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\UitIdUsersFactory.
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultureFeed;
use CultuurNet\UDB3\UiTID\CultureFeedUsers;
use CultuurNet\UDB3\UiTID\InMemoryCacheDecoratedUsers;
use CultuurNet\UDB3\UiTID\UsersInterface;

/**
 * Class UitIdUsersFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class UitIdUsersFactory {

  /**
   * Culturefeed.
   *
   * @var \Culturefeed
   */
  protected $culturefeed;

  /**
   * UitIdUsersFactory constructor.
   *
   * @param \CultureFeed $culturefeed
   *   Culturefeed.
   */
  public function __construct(CultureFeed $culturefeed) {
    $this->culturefeed = $culturefeed;
  }

  /**
   * Get the uitid users.
   *
   * @return \CultuurNet\UDB3\UiTID\UsersInterface
   *   The uitid users.
   */
  public function get() {
    return new CultureFeedUsers($this->culturefeed);
  }

}
