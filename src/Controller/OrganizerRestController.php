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
      $container->get('culturefeed_udb3.organizer_service'),
      $container->get('culturefeed_udb3.organizer_editing_service'),
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
   * Search for duplicates organizers.
   */
  public function searchDuplicates(Request $request, $title) {

    $postalcode = $request->query->get('postalcode');
    if (empty($postalcode)) {
      $results = $this->indexRepository->getOrganizersByTitle($title);
    }
    else {
      $results = $this->indexRepository->getOrganizersByTitleAndZip($title, $postalcode);
    }

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

}
