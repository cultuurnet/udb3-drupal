<?php

namespace Drupal\culturefeed_udb3\EventExport;

use Broadway\UuidGenerator\UuidGeneratorInterface;
use CultuurNet\UDB3\EventExport\EventExportService;
use CultuurNet\UDB3\EventExport\Notification\NotificationMailerInterface;
use CultuurNet\UDB3\Event\EventServiceInterface;
use CultuurNet\UDB3\Iri\IriGeneratorInterface;
use CultuurNet\UDB3\Search\ResultsGeneratorInterface;
use CultuurNet\UDB3\Search\SearchServiceInterface;

/**
 * Class EventExportServiceFactory.
 *
 * @package Drupal\culturefeed_udb3\EventExport
 */
class EventExportServiceFactory {

  /**
   * Create the event export service.
   *
   * @param \CultuurNet\UDB3\Event\EventServiceInterface $event_service
   *   The event service.
   * @param \CultuurNet\UDB3\Search\SearchServiceInterface $search_service
   *   The search service.
   * @param \Broadway\UuidGenerator\UuidGeneratorInterface $uuid_generator
   *   The uuid generator.
   * @param string $public_directory
   *   The public directory.
   * @param \CultuurNet\UDB3\Iri\IriGeneratorInterface $iri_generator
   *   The iri generator.
   * @param \CultuurNet\UDB3\EventExport\Notification\NotificationMailerInterface $mailer
   *   The notification mailer.
   * @param \CultuurNet\UDB3\Search\ResultsGeneratorInterface
   *   The search results generator.
   *
   * @return \CultuurNet\UDB3\EventExport\EventExportService
   *   The event export service.
   */
  public static function create(
    EventServiceInterface $event_service,
    SearchServiceInterface $search_service,
    UuidGeneratorInterface $uuid_generator,
    $public_directory,
    IriGeneratorInterface $iri_generator,
    NotificationMailerInterface $mailer,
    ResultsGeneratorInterface $results_generator
  ) {

    return new EventExportService(
      $event_service,
      $search_service,
      $uuid_generator,
      drupal_realpath($public_directory),
      $iri_generator,
      $mailer,
      $results_generator
    );

  }

}
