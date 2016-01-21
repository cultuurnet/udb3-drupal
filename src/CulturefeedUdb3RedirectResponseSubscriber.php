<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\EventSubscriber\RedirectResponseSubscriber.
 */

namespace Drupal\culturefeed_udb3;

use Drupal\Core\EventSubscriber\RedirectResponseSubscriber;

/**
 * Allows manipulation of the response object when performing a redirect.
 */
class CulturefeedUdb3RedirectResponseSubscriber extends RedirectResponseSubscriber {

  /**
   * Registers the methods in this class that should be listeners.
   *
   * @return array
   *   An array of event listener definitions.
   */
  static function getSubscribedEvents() {
    // Unsubscribe the checkRedirectUrl and sanitizeDestination subscriptions.
    return array();
  }
}
