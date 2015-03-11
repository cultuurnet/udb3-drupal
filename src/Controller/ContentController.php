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
    $results_query = db_select('culturefeed_udb3_index', 'i')
        ->fields('i', array('id', 'type', 'title', 'zip'))
        ->condition('i.uid', $user_id);
    $results = $results_query->execute();

    foreach ($results as $result) {
      // Get details
      $table = 'culturefeed_udb3_' . $result->type . '_store';
      $details_query = db_select($table, 's')
          ->fields('s', array('payload'))
          ->condition('s.uuid', $result->id);
      $details = $details_query->execute()->fetch();
      $result->details = json_decode($details->payload);
      $content['content'][] = $result;

    }
    return new JsonResponse($content);
  }
}
