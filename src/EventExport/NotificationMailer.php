<?php
/**
 * @file
 */

namespace Drupal\culturefeed_udb3\EventExport;


use CultuurNet\UDB3\EventExport\EventExportResult;
use CultuurNet\UDB3\EventExport\Notification\NotificationMailerInterface;
use Drupal\Core\Mail\MailManagerInterface;
use ValueObjects\Web\EmailAddress;

class NotificationMailer implements NotificationMailerInterface {

    /**
     * @var MailManagerInterface
     */
    protected $mailManager;

    /**
     * @param MailManagerInterface $mailManager
     */
    public function __construct(MailManagerInterface $mailManager)
    {
        $this->mailManager = $mailManager;
    }

    public function sendNotificationMail(
        EmailAddress $address,
        EventExportResult $eventExportResult
    ) {
        $this->mailManager->mail(
            'culturefeed_udb3',
            'event_export_ready',
            (string)$address,
            // @todo Handle language properly.
            'nl',
            [
                'location' => $eventExportResult->getUrl()
            ]
        );
    }

}
