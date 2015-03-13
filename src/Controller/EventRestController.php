<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\EventRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\UDB3\Event\Event;
use CultuurNet\UDB3\Event\EventEditingServiceInterface;
use CultuurNet\UDB3\Event\EventType;
use CultuurNet\UDB3\EventServiceInterface;
use CultuurNet\UDB3\Keyword;
use CultuurNet\UDB3\Language;
use CultuurNet\UDB3\Location;
use CultuurNet\UDB3\Theme;
use CultuurNet\UDB3\Title;
use CultuurNet\UDB3\UsedKeywordsMemory\DefaultUsedKeywordsMemoryService;
use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EventRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class EventRestController extends OfferRestBaseController {

  const IMAGE_UPLOAD_DIR = 'public://events';

  /**
   * The search service.
   *
   * @var PullParsingSearchService;
   */
  protected $searchService;

  /**
   * The event editor
   * @var EventEditingServiceInterface
   */
  protected $editor;

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
      $container->get('culturefeed_udb3.event.used_keywords_memory'),
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
   * @param DefaultUsedKeywordsMemoryService $used_keywords_memory
   *   The event tagger.
   * @param CultureFeed_User $user
   *   The culturefeed user.
   */
  public function __construct(
    EventServiceInterface $event_service,
    EventEditingServiceInterface $event_editor,
    DefaultUsedKeywordsMemoryService $used_keywords_memory,
    CultureFeed_User $user
  ) {
    $this->eventService = $event_service;
    $this->editor = $event_editor;
    $this->usedKeywordsMemory = $used_keywords_memory;
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
    $event = $this->getItem($cdbid);

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
      $command_id = $this->editor->translateTitle(
        $cdbid,
        new Language($language),
        $body_content->title
      );

      $response->setData(['commandId' => $command_id]);
    } catch (Exception $e) {
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

    // If it's the main language, it should use updateDescription instead of translate.
    if ($language == Event::MAIN_LANGUAGE_CODE) {
      return parent::updateDescription($request, $cdbid, $language);
    }

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    if (!$body_content->description) {
      return new JsonResponse(['error' => "description required"], 400);
    }

    try {

      $command_id = $this->editor->translateDescription(
        $cdbid,
        new Language($language),
        $body_content->description
      );

      $response->setData(['commandId' => $command_id]);
    } catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Adds a keyword.
   *
   * @param Request $request
   *   The request.
   * @param string $cdbid
   *   The event id.
   *
   * @return JsonResponse
   *   The response.
   */
  public function addKeyword(Request $request, $cdbid) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    try {

      $keyword = new Keyword($body_content->keyword);
      $command_id = $this->editor->tag(
        $cdbid,
        $keyword
      );

      $user = $this->user;
      $this->usedKeywordsMemory->rememberKeywordUsed(
        $user->id,
        $keyword
      );

      $response->setData(['commandId' => $command_id]);
    } catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Deletes a keyword.
   *
   * @param Request $request
   *   The request.
   * @param string $cdbid
   *   The event id.
   * @param string $keyword
   *   The keyword.
   *
   * @return JsonResponse
   *   The response.
   */
  public function deleteKeyword(Request $request, $cdbid, $keyword) {

    $response = new JsonResponse();

    try {
      $command_id = $this->editor->eraseTag(
        $cdbid,
        new Keyword($keyword)
      );

      $response->setData(['commandId' => $command_id]);
    } catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Create a new event.
   */
  public function createEvent(Request $request) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    try {

      if (empty($body_content->name) || empty($body_content->type) || empty($body_content->location) || empty($body_content->calendarType)) {
        throw new \InvalidArgumentException('Required fields are missing');
      }

      $calendar = $this->initCalendarForCreate($body_content);

      $theme = null;
      if (!empty($body_content->theme) && !empty($body_content->theme->id)) {
        $theme = new Theme($body_content->theme->id, $body_content->theme->label);
      }

      $event_id = $this->editor->createEvent(
        new Title($body_content->name->nl),
        new EventType($body_content->type->id, $body_content->type->label),
        new Location($body_content->location->id, $body_content->location->name, $body_content->location->address->addressCountry, $body_content->location->address->addressLocality, $body_content->location->address->postalCode, $body_content->location->address->streetAddress),
        $calendar,
        $theme
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
    } catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;
  }

  /**
   * Get the image destination.
   */
  public function getImageDestination($id) {
    return self::IMAGE_UPLOAD_DIR . '/' . $id;
  }

  /**
   * Get the detail of an item.
   */
  public function getItem($id) {
    return $this->eventService->getEvent($id);
  }

}
