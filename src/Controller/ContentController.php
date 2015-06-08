<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\ContentController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
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
      $container->get('culturefeed.current_user')
    );
  }

  /**
   * Constructs a ContentController.
   *
   * @param CultureFeed_User $user
   *   The culturefeed user.
   */
  public function __construct(CultureFeed_User $user) {
    $this->user = $user;
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
    $results_query->range(0, 50);
    $results = $results_query->execute();

    $grouped_results = array();
    // Loop through results. Events come first, after that places.
    // Places are listed first, then the events for that place.
    foreach ($results as $result) {

      $table = 'culturefeed_udb3_' . $result->type . '_document_repository';
      $details_query = db_select($table, 'd')
          ->fields('d', array('body'))
          ->condition('d.id', $result->id);
      $details = $details_query->execute()->fetch();
      if ($details) {
        $jsonLd = json_decode($details->body);
        $jsonLd->type = $result->type;
        $jsonLd->id = $result->id;

        if ($result->type == 'event') {
          $grouped_results[$result->place][] = $jsonLd;
        }
        else {
          $content['content'][] = $jsonLd;
          if (!empty($grouped_results[$result->id])) {
            $content['content'] = array_merge($content['content'], $grouped_results[$result->id]);
          }
        }

      }

    }

    return new JsonResponse($content);
  }

}
