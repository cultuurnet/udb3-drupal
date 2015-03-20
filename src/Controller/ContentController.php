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
        ->fields('i', array('id', 'type', 'created_on'))
        ->condition('i.uid', $user_id)
        ->condition('i.type', 'organizer', '!=')
        ->orderBy('created_on', 'DESC')
        ->range(0, 50);
    $results = $results_query->execute();

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
        $content['content'][] = $jsonLd;
      }

    }

    return new JsonResponse($content);
  }
}
