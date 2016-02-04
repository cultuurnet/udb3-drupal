<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\OrganizerRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\UDB3\Address;
use CultuurNet\UDB3\EntityServiceInterface;
use CultuurNet\UDB3\Event\EventType;
use CultuurNet\UDB3\Event\ReadModel\Relations\RepositoryInterface;
use CultuurNet\UDB3\Facility;
use CultuurNet\UDB3\Place\PlaceEditingServiceInterface;
use CultuurNet\UDB3\Theme;
use CultuurNet\UDB3\Title;
use Drupal;
use Drupal\culturefeed_udb3\EventRelationsRepository;
use Drupal\file\FileUsage\FileUsageInterface;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\Exception;

/**
 * Class PlaceRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class PlaceRestController extends OfferRestBaseController {

  const IMAGE_UPLOAD_DIR = 'public://places';

  /**
   * The entity service.
   *
   * @var EntityServiceInterface
   */
  protected $entityService;

  /**
   * The place editor.
   *
   * @var PlaceEditingServiceInterface
   */
  protected $editor;

  /**
   * The culturefeed user.
   *
   * @var Culturefeed_User
   */
  protected $user;

  /**
   * The event relations repository.
   *
   * @var EventRelationsRepository
   */
  protected $eventRelationsRepository;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {

    return new static(
      $container->get('culturefeed_udb3.place_service'),
      $container->get('culturefeed_udb3.place_editing_service'),
      $container->get('culturefeed_udb3.event_relations_repository'),
      $container->get('culturefeed.current_user'),
      $container->get('file.usage')
    );
  }

  /**
   * Constructs a RestController.
   *
   * @param EntityServiceInterface $entity_service
   *   The entity service.
   * @param CultureFeed_User $user
   *   The culturefeed user.
   */
  public function __construct(
    EntityServiceInterface $entity_service,
    PlaceEditingServiceInterface $place_editor,
    RepositoryInterface $event_relations_repository,
    CultureFeed_User $user,
    FileUsageInterface $fileUsage
  ) {
    $this->entityService = $entity_service;
    $this->editor = $place_editor;
    $this->eventRelationsRepository = $event_relations_repository;
    $this->user = $user;
    $this->fileUsage = $fileUsage;
  }

  /**
   * Creates a json-ld response.
   *
   * @return BinaryFileResponse
   *   The response.
   */
  public function placeContext() {
    $response = new BinaryFileResponse('/udb3/api/1.0/place.jsonld');
    $response->headers->set('Content-Type', 'application/ld+json');
    return $response;
  }

  /**
   * Remove a place.
   */
  public function deletePlace(Request $request, $cdbid) {

    $response = new JsonResponse();

    try {

      if (empty($cdbid)) {
        throw new InvalidArgumentException('Required fields are missing');
      }

      $result = $this->editor->deletePlace($cdbid);
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
    return $this->entityService->getEntity($id);
  }

  /**
   * Get the events for a given place.
   * @return type
   */
  public function getEvents(Request $request, $cdbid) {

    $response = new JsonResponse();

    // Load all event relations from the database.
    $events = $this->eventRelationsRepository->getEventsLocatedAtPlace($cdbid);
    if (!empty($events)) {

      $urlGenerator = Drupal::service('url_generator');
      $iriGenerator = Drupal::service('culturefeed_udb3.iri_generator');

      $data = ['events' => []];

      foreach ($events as $eventId) {
        $data['events'][] = [
          '@id' => $iriGenerator->iri($eventId),
        ];
      }

      $response->setData($data);
    }

    return $response;
  }

}
