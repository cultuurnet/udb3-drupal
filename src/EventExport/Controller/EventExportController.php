<?php
/**
 * @file
 */

namespace Drupal\culturefeed_udb3\EventExport\Controller;


use Broadway\CommandHandling\CommandBusInterface;
use CultuurNet\UDB3\EventExport\Command\ExportEventsAsJsonLD;
use CultuurNet\UDB3\EventExport\Command\ExportEventsAsOOXML;
use CultuurNet\UDB3\EventExport\EventExportQuery;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\Web\EmailAddress;

class EventExportController extends ControllerBase
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @param ContainerInterface $container
     * @return static
     */
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('culturefeed_udb3.event_command_bus')
        );
    }

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus) {
        $this->commandBus = $commandBus;
    }

    public function exportAsJSONLD(Request $request) {

        $body = json_decode($request->getContent());

        $email = isset($body->email) ? new EmailAddress($body->email) : null;
        $selection = isset($body->selection) ? $body->selection : null;
        $include = isset($body->include) ? $body->include : null;

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

    public function exportAsOOXML(Request $request) {
        $body = json_decode($request->getContent());

        $email = isset($body->email) ? new EmailAddress($body->email) : null;
        $selection = isset($body->selection) ? $body->selection : null;
        $include = isset($body->include) ? $body->include : null;

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
}
