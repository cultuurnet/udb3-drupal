<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\CulturefeedUdb3RedirectResponseSubscriber.
 */

namespace Drupal\culturefeed_udb3;

use Drupal\Core\EventSubscriber\RedirectResponseSubscriber;
use Symfony\Component\HttpKernel\KernelEvents;

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
  public static function getSubscribedEvents() {
    // Unsubscribe the checkRedirectUrl and sanitizeDestination subscriptions.
    $subscribed_events = parent::getSubscribedEvents();
    $subscribed_events[KernelEvents::RESPONSE] = array_filter($subscribed_events[KernelEvents::RESPONSE], function($subscriber) {
      return $subscriber[0] != 'checkRedirectUrl';
    });

    $subscribed_events[KernelEvents::REQUEST] = array_filter($subscribed_events[KernelEvents::REQUEST], function($subscriber) {
      return $subscriber[0] != 'sanitizeDestination';
    });
    
    // Remove empty arrays completely.
    $subscribed_events = array_filter($subscribed_events, function($subscribers) {
      return !empty($subscribers);
    });

    return $subscribed_events;
  }

}
