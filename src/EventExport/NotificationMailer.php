<?php

namespace Drupal\culturefeed_udb3\EventExport;

use CultuurNet\UDB3\EventExport\EventExportResult;
use CultuurNet\UDB3\EventExport\Notification\NotificationMailerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use ValueObjects\Web\EmailAddress;

/**
 * Class NotificationMailer.
 *
 * @package Drupal\culturefeed_udb3\EventExport
 */
class NotificationMailer implements NotificationMailerInterface {

  /**
   * The mail manager.
   *
   * @var MailManagerInterface
   */
  protected $mailManager;

  /**
   * NotificationMailer constructor.
   *
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager.
   */
  public function __construct(MailManagerInterface $mail_manager) {
    $this->mailManager = $mail_manager;
  }

  /**
   * Send the notification mail.
   *
   * @param \ValueObjects\Web\EmailAddress $address
   *   The address.
   * @param \CultuurNet\UDB3\EventExport\EventExportResult $eventExportResult
   *   The event export result.
   */
  public function sendNotificationMail(
    EmailAddress $address,
    EventExportResult $eventExportResult
  ) {
    $this->mailManager->mail(
      'culturefeed_udb3',
      'event_export_ready',
      (string) $address,
      // @todo Handle language properly.
      'nl',
      [
        'location' => $eventExportResult->getUrl(),
      ]
    );
  }

}
