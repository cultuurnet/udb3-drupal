<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\ContentController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ContentController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class ContentController extends ControllerBase {

  /**
   * The event document repository.
   *
   * @var \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface
   */
  protected $eventRepository;

  /**
   * The place document repository.
   *
   * @var \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface
   */
  protected $placeRepository;

  /**
   * The document repositories.
   *
   * @var array
   */
  protected $repositories;

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
      $container->get('culturefeed.current_user'),
      $container->get('culturefeed_udb3.event_cache_document_repositroy'),
      $container->get('culturefeed_udb3.place_cache_document_repositroy')
    );
  }

  /**
   * Constructs a ContentController.
   *
   * @param CultureFeed_User $user
   *   The culturefeed user.
   * @param \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface $event_repository
   *   The event document repository.
   * @param \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface $place_repository
   *   The place document repository.
   */
  public function __construct(CultureFeed_User $user, DocumentRepositoryInterface $event_repository, DocumentRepositoryInterface $place_repository) {
    $this->user = $user;
    $this->eventRepository = $event_repository;
    $this->placeRepository = $place_repository;
    $this->repositories = array(
      'event' => $this->eventRepository,
      'place' => $this->placeRepository,
    );
  }

  /**
   * Load the udb3 content for the current user.
   *
   * @return JsonResponse
   */
  public function contentForCurrentUser() {

    // Get udb3 content for the current user.
    $user_id = $this->user->id;
    $content = array();
    $results_query = db_select('culturefeed_udb3_index', 'i');
    $results_query->leftJoin('culturefeed_udb3_event_relations', 'r', 'r.event = i.id');
    $results_query->fields('i', array('id', 'type', 'created_on'));
    $results_query->fields('r', array('place'));
    $results_query->condition('i.uid', $user_id);
    $results_query->condition('i.type', 'organizer', '!=');
    $results_query->orderBy('type', 'ASC');
    $results_query->orderBy('created_on', 'DESC');
    $results = $results_query->execute();

    $grouped_results = array();
    // Loop through results. Events come first, after that places.
    // Places are listed first, then the events for that place.
    foreach ($results as $result) {

      /* @var \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface $repository */
      $repository = $this->repositories[$result->type];
      $details = $repository->get($result->id);
      if ($details) {
        $jsonLd = $details->getBody();
        $jsonLd->type = $result->type;
        $jsonLd->id = $result->id;

        if ($result->type == 'event') {
          $grouped_results[$result->place][] = $jsonLd;
        }
        else {
          $content['content'][] = $jsonLd;
          if (!empty($grouped_results[$result->id])) {
            $content['content'] = array_merge($content['content'], $grouped_results[$result->id]);
            unset($grouped_results[$result->id]);
          }
        }

      }

    }

    // Also add events that don't belong to a place created by current user.
    foreach ($grouped_results as $result) {
      $content['content'] = array_merge($content['content'], $result);
    }

    return new JsonResponse($content);
  }

}
