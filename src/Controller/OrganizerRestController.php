<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\OrganizerRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\UDB3\Address;
use CultuurNet\UDB3\EntityServiceInterface;
use CultuurNet\UDB3\Title;
use CultuurNet\UDB3\ReadModel\Index\RepositoryInterface;
use CultuurNet\UDB3\Organizer\OrganizerEditingServiceInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrganizerRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class OrganizerRestController extends ControllerBase {

  /**
   * The entity service.
   *
   * @var EntityServiceInterface
   */
  protected $entityService;

  /**
   * The organizer editor
   * @var OrganizerEditingServiceInterface
   */
  protected $organizerEditor;

  /**
   * The index repository.
   *
   * @var RepositoryInterface
   */
  protected $indexRepository;

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
      $container->get('culturefeed_udb3.organizer.service'),
      $container->get('culturefeed_udb3.organizer.editor'),
      $container->get('culturefeed_udb3.udb3_index_repository'),
      $container->get('culturefeed.current_user')
    );
  }

  /**
   * Constructs a RestController.
   *
   * @param EntityServiceInterface $entity_service
   *   The entity service.
   * @param RepositoryInterface $indexRepository
   *   The index repository.
   * @param CultureFeed_User $user
   *   The culturefeed user.
   */
  public function __construct(
    EntityServiceInterface $entity_service,
    OrganizerEditingServiceInterface $organizerEditor,
    RepositoryInterface $indexRepository,
    CultureFeed_User $user
  ) {
    $this->entityService = $entity_service;
    $this->organizerEditor = $organizerEditor;
    $this->indexRepository = $indexRepository;
    $this->user = $user;
  }

  /**
   * Creates a json-ld response.
   *
   * @return BinaryFileResponse
   *   The response.
   */
  public function organizerContext() {
    $response = new BinaryFileResponse('/udb3/api/1.0/organizer.jsonld');
    $response->headers->set('Content-Type', 'application/ld+json');
    return $response;
  }

  /**
   * Returns an organizer.
   *
   * @param string $cdbid
   *   The place id.
   *
   * @return JsonLdResponse
   *   The response.
   */
  public function details($cdbid) {

    $organizer = $this->entityService->getEntity($cdbid);

    $response = JsonResponse::create()
      ->setContent($organizer)
      ->setPublic()
      ->setClientTtl(60 * 30)
      ->setTtl(60 * 5);

    return $response;

  }

  /**
   * Suggest organizers based on a search value.
   * @param string $title
   *
   * @return JsonResponse
   *   The response.
   */
  public function suggest($title) {

    $query = db_select('culturefeed_udb3_index', 'i');
    $query->condition('title', '%' . db_like($title) . '%', 'LIKE');
    $query->condition('type', 'organizer');
    $query->range(0, 10);
    $query->fields('i', array('id', 'title'));
    $result = $query->execute();

    $matches = array();
    foreach ($result as $row) {
      $organizer = new \stdClass();
      $organizer->id = $row->id;
      $organizer->name = $row->title;
      $matches[] = $organizer;
    }

    return JsonResponse::create()
      ->setContent(json_encode($matches))
      ->setPublic()
      ->setClientTtl(60 * 30)
      ->setTtl(60 * 5);

  }

  /**
   * Search for duplicates organizers.
   */
  public function searchDuplicates($title, $zip) {

    $results = $this->indexRepository->getOrganizersByTitleAndZip($title, $zip);

    $duplicates = array();
    foreach ($results as $entity_id) {
      $result = new \stdClass();
      $result->id = $entity_id;
      $duplicates[] = $result;
    }

    return JsonResponse::create()
      ->setContent(json_encode($duplicates))
      ->setPublic()
      ->setClientTtl(60 * 30)
      ->setTtl(60 * 5);

  }

  /**
   * Create a new organizer.
   */
  public function createOrganizer(Request $request) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    try {

      if (empty($body_content->name) || empty($body_content->street) || empty($body_content->number) || empty($body_content->city) || empty($body_content->postalCode) || empty($body_content->country)) {
        throw new \InvalidArgumentException('Required fields are missing');
      }

      $addresses = array();
      $streetAddress = $body_content->street . ' ' . $body_content->number;
      $addresses[] = new Address($streetAddress, $body_content->postalCode, $body_content->city, 'BE');

      $phones = array();
      $emails = array();
      $urls = array();
      if (!empty($body_content->contact)) {
        foreach ($body_content->contact as $contactInfo) {
          if ($contactInfo->type == 'phone') {
            $phones[] = $contactInfo->value;
          }
          elseif ($contactInfo->type == 'email') {
            $emails[] = $contactInfo->value;
          }
          elseif ($contactInfo->type == 'url') {
            $urls[] = $contactInfo->value;
          }
        }
      }

      $organizer_id = $this->organizerEditor->createOrganizer(
        new Title($body_content->name),
        $addresses,
        $phones,
        $emails,
        $urls
      );

      $response->setData(
        [
          'organizerId' => $organizer_id,
          'url' => $this->getUrlGenerator()->generateFromRoute(
            'culturefeed_udb3.organizer',
            ['cdbid' => $organizer_id],
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
