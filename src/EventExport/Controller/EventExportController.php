<?php

namespace Drupal\culturefeed_udb3\EventExport\Controller;

use Broadway\CommandHandling\CommandBusInterface;
use CultuurNet\UDB3\EventExport\Command\ExportEventsAsJsonLD;
use CultuurNet\UDB3\EventExport\Command\ExportEventsAsOOXML;
use CultuurNet\UDB3\EventExport\Command\ExportEventsAsPDFJSONDeserializer;
use CultuurNet\UDB3\EventExport\EventExportQuery;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\Web\EmailAddress;
use ValueObjects\String\String;

/**
 * Class EventExportController.
 *
 * @package Drupal\culturefeed_udb3\EventExport\Controller
 */
class EventExportController extends ControllerBase {

  /**
   * The command bus.
   *
   * @var CommandBusInterface
   */
  protected $commandBus;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('culturefeed_udb3.event_command_bus')
    );
  }

  /**
   * EventExportController constructor.
   *
   * @param \Broadway\CommandHandling\CommandBusInterface $command_bus
   *   The command bus.
   */
  public function __construct(CommandBusInterface $command_bus) {
    $this->commandBus = $command_bus;
  }

  /**
   * Export as JSON LD.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\Response|static
   *   The response.
   */
  public function exportAsJsonLd(Request $request) {

    $body = json_decode($request->getContent());

    $email = isset($body->email) ? new EmailAddress($body->email) : NULL;
    $selection = isset($body->selection) ? $body->selection : NULL;
    $include = isset($body->include) ? $body->include : NULL;

    $command = new ExportEventsAsJsonLD(
      new EventExportQuery(
        $body->query
      ),
      $email,
      $selection,
      $include
    );

    $commandId = $this->commandBus->dispatch($command);

    return JsonResponse::create(
      ['commandId' => $commandId]
    );

  }

  /**
   * Export as OOXML.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\Response|static
   *   The response.
   */
  public function exportAsOoXml(Request $request) {

    $body = json_decode($request->getContent());

    $email = isset($body->email) ? new EmailAddress($body->email) : NULL;
    $selection = isset($body->selection) ? $body->selection : NULL;
    $include = isset($body->include) ? $body->include : NULL;

    $command = new ExportEventsAsOOXML(
      new EventExportQuery(
        $body->query
      ),
      $email,
      $selection,
      $include
    );

    $commandId = $this->commandBus->dispatch($command);

    return JsonResponse::create(
      ['commandId' => $commandId]
    );

  }

  /**
   * Export as PDF.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\Response|static
   *   The response.
   */
  public function exportAsPdf(Request $request) {

    $deserializer = new ExportEventsAsPDFJSONDeserializer();
    $jsonString = new String($request->getContent());
    $command = $deserializer->deserialize($jsonString);
    $commandId = $this->commandBus->dispatch($command);

    return JsonResponse::create(
      ['commandId' => $commandId]
    );

  }

}
