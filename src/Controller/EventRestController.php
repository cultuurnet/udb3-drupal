<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\EventRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultuurNet\UDB3\Event\EventEditingServiceInterface;
use CultuurNet\UDB3\Event\Title;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CultuurNet\UDB3\Search\PullParsingSearchService;
use CultuurNet\UDB3\EventServiceInterface;
use CultuurNet\UDB3\Language;
use CultuurNet\UDB3\Label;
use CultuurNet\UDB3\UsedLabelsMemory\DefaultUsedLabelsMemoryService;
use CultureFeed_User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use CultuurNet\UDB3\Symfony\JsonLdResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class EventRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class EventRestController extends ControllerBase{

  /**
   * The search service.
   *
   * @var PullParsingSearchService;
   */
  protected $searchService;

  /**
   * The event service.
   *
   * @var EventServiceInterface
   */
  protected $eventService;

  /**
   * The culturefeed user.
   *
   * @var Culturefeed_User
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('culturefeed_udb3.event.service'),
      $container->get('culturefeed_udb3.event.editor'),
      $container->get('culturefeed_udb3.event.used_labels_memory'),
      $container->get('culturefeed.current_user')
    );
  }

  /**
   * Constructs a RestController.
   *
   * @param EventServiceInterface $event_service
   *   The event service.
   * @param EventEditingServiceInterface $event_editor
   *   The event editor.
   * @param DefaultUsedLabelsMemoryService $used_labels_memory
   *   The event labeller.
   * @param CultureFeed_User $user
   *   The culturefeed user.
   */
  public function __construct(
    EventServiceInterface $event_service,
    EventEditingServiceInterface $event_editor,
    DefaultUsedLabelsMemoryService $used_labels_memory,
    CultureFeed_User $user
  ) {
    $this->eventService = $event_service;
    $this->eventEditor = $event_editor;
    $this->usedLabelsMemory = $used_labels_memory;
    $this->user = $user;
  }

  /**
   * Creates a json-ld response.
   *
   * @return BinaryFileResponse
   *   The response.
   */
  public function eventContext() {
    $response = new BinaryFileResponse('/udb3/api/1.0/event.jsonld');
    $response->headers->set('Content-Type', 'application/ld+json');
    return $response;
  }

  /**
   * Returns an event.
   *
   * @param string $cdbid
   *   The event id.
   *
   * @return JsonLdResponse
   *   The response.
   */
  public function details($cdbid) {
    $event = $this->eventService->getEvent($cdbid);

    $response = JsonResponse::create()
      ->setContent($event)
      ->setPublic()
      ->setClientTtl(60 * 30)
      ->setTtl(60 * 5);

    return $response;

  }

  /**
   * Modifies the event tile.
   *
   * @param Request $request
   *   The request.
   * @param string $cdbid
   *   The event id.
   * @param string $language
   *   The event language.
   *
   * @return JsonResponse
   *   The response.
   */
  public function title(Request $request, $cdbid, $language) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    if (!$body_content->title) {
      return new JsonResponse(['error' => "title required"], 400);
    }

    try {
      $command_id = $this->eventEditor->translateTitle(
        $cdbid,
        new Language($language),
        $body_content->title
      );

      $response->setData(['commandId' => $command_id]);
    } catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Modifies the event description.
   *
   * @param Request $request
   *   The request.
   * @param string $cdbid
   *   The event id.
   * @param string $language
   *   The event language.
   *
   * @return JsonResponse
   *   The response.
   */
  public function description(Request $request, $cdbid, $language) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    if (!$body_content->description) {
      return new JsonResponse(['error' => "description required"], 400);
    }

    try {
      $command_id = $this->eventEditor->translateDescription(
        $cdbid,
        new Language($language),
        $body_content->description
      );

      $response->setData(['commandId' => $command_id]);
    } catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Adds a label.
   *
   * @param Request $request
   *   The request.
   * @param string $cdbid
   *   The event id.
   *
   * @return JsonResponse
   *   The response.
   */
  public function addLabel(Request $request, $cdbid) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    try {

      $label = new Label($body_content->label);
      $command_id = $this->eventEditor->label(
        $cdbid,
        $label
      );

      $user = $this->user;
      $this->usedLabelsMemory->rememberLabelUsed(
        $user->id,
        $label
      );

      $response->setData(['commandId' => $command_id]);
    } catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Deletes a label.
   *
   * @param Request $request
   *   The request.
   * @param string $cdbid
   *   The event id.
   * @param string $label
   *   The label.
   *
   * @return JsonResponse
   *   The response.
   */
  public function deleteLabel(Request $request, $cdbid, $label) {

    $response = new JsonResponse();

    try {
      $command_id = $this->eventEditor->unlabel(
        $cdbid,
        new Label($label)
      );

      $response->setData(['commandId' => $command_id]);
    } catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  public function newEvent(Request $request) {
    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    try {
      $event_id = $this->eventEditor->createEvent(
        new Title($body_content->name),
        $body_content->location,
        \DateTime::createFromFormat(
          \DateTime::ISO8601,
          $body_content->date
        )
      );

      $response->setData(
        [
          'eventId' => $event_id,
          'url' => $this->getUrlGenerator()->generateFromRoute(
            'culturefeed_udb3.event',
            ['cdbid' => $event_id],
            ['absolute' => TRUE]
          ),
        ]
      );
    } catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;
  }

}
