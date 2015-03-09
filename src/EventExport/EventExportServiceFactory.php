<?php
/**
 * @file
 */

namespace Drupal\culturefeed_udb3\EventExport;


use Broadway\UuidGenerator\UuidGeneratorInterface;
use CultuurNet\UDB3\EventExport\EventExportService;
use CultuurNet\UDB3\EventExport\Notification\NotificationMailerInterface;
use CultuurNet\UDB3\EventServiceInterface;
use CultuurNet\UDB3\Iri\IriGeneratorInterface;
use CultuurNet\UDB3\Search\SearchServiceInterface;

class EventExportServiceFactory {

    public static function create(
        EventServiceInterface $eventService,
        SearchServiceInterface $searchService,
        UuidGeneratorInterface $uuidGenerator,
        $publicDirectory,
        IriGeneratorInterface $iriGenerator,
        NotificationMailerInterface $mailer
    ) {
        return new EventExportService(
            $eventService,
            $searchService,
            $uuidGenerator,
            drupal_realpath($publicDirectory),
            $iriGenerator,
            $mailer
        );
    }
}
