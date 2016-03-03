<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\EventRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UDB3\Event\Event;
use CultuurNet\UDB3\Event\EventEditingServiceInterface;
use CultuurNet\UDB3\Event\EventType;
use CultuurNet\UDB3\EventServiceInterface;
use CultuurNet\UDB3\Label;
use CultuurNet\UDB3\Language;
use CultuurNet\UDB3\Location;
use CultuurNet\UDB3\Theme;
use CultuurNet\UDB3\Title;
use CultuurNet\UDB3\UsedLabelsMemory\DefaultUsedLabelsMemoryService;
use Drupal;
use Drupal\file\FileUsage\FileUsageInterface;
use Exception;
use InvalidArgumentException;
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
   * The file usage interface.
   * @var FileUsageInterface
   */
  protected $fileUsage;

  /**
   * The used labels memory.
   *
   * @var DefaultUsedLabelsMemoryService
   */
  protected $usedLabelsMemory;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('culturefeed_udb3.event_service'),
      $container->get('culturefeed_udb3.event_editor'),
      $container->get('culturefeed_udb3.used_labels_memory'),
      $container->get('culturefeed.current_user'),
      $container->get('file.usage')
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
   * @param FileUsageInterface $fileUsage
   *   The file usage.
   */
  public function __construct(
    EventServiceInterface $event_service,
    EventEditingServiceInterface $event_editor,
    DefaultUsedLabelsMemoryService $used_labels_memory,
    CultureFeed_User $user,
    FileUsageInterface $fileUsage
  ) {
    $this->eventService = $event_service;
    $this->editor = $event_editor;
    $this->usedLabelsMemory = $used_labels_memory;
    $this->user = $user;
    $this->fileUsage = $fileUsage;
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
   * Remove an event.
   */
  public function deleteEvent(Request $request, $cdbid) {

    $response = new JsonResponse();

    try {

      if (empty($cdbid)) {
        throw new InvalidArgumentException('Required fields are missing');
      }

      $result = $this->editor->deleteEvent($cdbid);
      $response->setData(['result' => $result]);

    } catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
      watchdog_exception('udb3', $e);
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
