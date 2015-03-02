<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\OrganizerRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\UDB3\Calendar;
use CultuurNet\UDB3\EntityServiceInterface;
use CultuurNet\UDB3\Event\Event;
use CultuurNet\UDB3\Event\EventType;
use CultuurNet\UDB3\Event\Title;
use CultuurNet\UDB3\Location;
use CultuurNet\UDB3\Place\PlaceEditingServiceInterface;
use CultuurNet\UDB3\Theme;
use Drupal\Core\Controller\ControllerBase;
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
class PlaceRestController extends ControllerBase {

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
  protected $placeEditor;

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
      $container->get('culturefeed_udb3.place.service'),
      $container->get('culturefeed_udb3.place.editor'),
      $container->get('culturefeed.current_user')
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
    CultureFeed_User $user
  ) {
    $this->entityService = $entity_service;
    $this->placeEditor = $place_editor;
    $this->user = $user;
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
   * Returns a place.
   *
   * @param string $cdbid
   *   The place id.
   *
   * @return JsonLdResponse
   *   The response.
   */
  public function details($cdbid) {

    $place = $this->entityService->getEntity($cdbid);

    $response = JsonResponse::create()
      ->setContent($place)
      ->setPublic()
      ->setClientTtl(60 * 30)
      ->setTtl(60 * 5);

    return $response;

  }

  /**
   * Create a new place.
   */
  public function createPlace(Request $request) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    try {

      if (empty($body_content->name) || empty($body_content->type) || empty($body_content->location) || empty($body_content->calendarType)) {
        throw new InvalidArgumentException('Required fields are missing');
      }

      $theme = null;
      if (!empty($body_content->theme) && !empty($body_content->theme->id)) {
        $theme = new Theme($body_content->theme->id, $body_content->theme->label);
      }
      $event_id = $this->placeEditor->createPlace(
        new Title($body_content->name->nl),
        new EventType($body_content->type->id, $body_content->type->label),
        new Location($body_content->location->name, $body_content->location->address->addressCountry, $body_content->location->address->addressLocality, $body_content->location->address->postalCode, $body_content->location->address->streetAddress),
        new Calendar($body_content->calendarType, $body_content->startDate, $body_content->endDate, $body_content->timestamps, $body_content->openingHours),
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
   * Update the description property.
   *
   * @param Request $request
   * @param type $cdbid
   * @return JsonResponse
   */
  public function updateDescription(Request $request, $cdbid, $language) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    if (!$body_content->description) {
      return new JsonResponse(['error' => "description required"], 400);
    }

    try {

      // If it's the main language, it should use updateDescription instead of translate.
      if ($language == Event::MAIN_LANGUAGE_CODE) {

        $command_id = $this->placeEditor->updateDescription(
          $cdbid,
          $body_content->description
        );

      }
      else {
        return new JsonResponse(['error' => "Translating places is not supported yet"], 400);
      }

      $response->setData(['commandId' => $command_id]);
    } catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Update the typicalAgeRange property.
   *
   * @param Request $request
   * @param type $cdbid
   * @return JsonResponse
   */
  public function updateTypicalAgeRange(Request $request, $cdbid) {

    $body_content = json_decode($request->getContent());
    if (empty($body_content->typicalAgeRange)) {
      return new JsonResponse(['error' => "typicalAgeRange required"], 400);
    }

    $response = new JsonResponse();
    try {
      $command_id = $this->placeEditor->updateTypicalAgeRange($cdbid, $body_content->typicalAgeRange);
      $response->setData(['commandId' => $command_id]);
    }
    catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

}
