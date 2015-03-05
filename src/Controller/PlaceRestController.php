<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\OrganizerRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\UDB3\Address;
use CultuurNet\UDB3\Calendar;
use CultuurNet\UDB3\EntityServiceInterface;
use CultuurNet\UDB3\Event\EventType;
use CultuurNet\UDB3\Place\PlaceEditingServiceInterface;
use CultuurNet\UDB3\Theme;
use CultuurNet\UDB3\Title;
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
    $this->editor = $place_editor;
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

      if (empty($body_content->name) || empty($body_content->type)) {
        throw new InvalidArgumentException('Required fields are missing');
      }

      $calendar = $this->initCalendarForCreate($body_content);

      $theme = null;
      if (!empty($body_content->theme) && !empty($body_content->theme->id)) {
        $theme = new Theme($body_content->theme->id, $body_content->theme->label);
      }
        
      $place_id = $this->editor->createPlace(
        new Title($body_content->name->nl),
        new EventType($body_content->type->id, $body_content->type->label),
        new Address($body_content->location->address->streetAddress, $body_content->location->address->postalCode, $body_content->location->address->addressLocality, $body_content->location->address->addressCountry),
        $calendar,
        $theme
      );

      $response->setData(
        [
          'placeId' => $place_id,
          'url' => $this->getUrlGenerator()->generateFromRoute(
            'culturefeed_udb3.place',
            ['cdbid' => $place_id],
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

}
