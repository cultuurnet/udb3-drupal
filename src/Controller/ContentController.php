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
    $results_query = db_select('culturefeed_udb3_index', 'i');
    $results_query->fields('i', array('id', 'type', 'created_on'));
    $results_query->condition('i.uid', $user_id);
    //$results_query->condition('i.type', ['event', 'place'], 'IN');
    // @todo: order on last updated time DESC
    $results_query->orderBy('created_on', 'DESC');
    // @todo: introduce pagination
    $results = $results_query->execute()->fetchAll();

    $lastUpdatedItems = array_map(
        function ($result) {
          $jsonLd = $this->fetchJSONLD($result);

          if (!$jsonLd) {
            return null;
          }

          $jsonLd->type = $result->type;
          $jsonLd->id = $result->id;

          return $jsonLd;
      },
      $results
    );

    $lastUpdatedItems = array_filter($lastUpdatedItems);

    // @todo Use Hydra
    return new JsonResponse(['member' => $lastUpdatedItems]);
  }

  private function fetchJSONLD($result)
  {
    $table = $this->documentRepositoryTable($result->type);
    $details_query = db_select($table, 'd')
        ->fields('d', array('body'))
        ->condition('d.id', $result->id);
    $details = $details_query->execute()->fetch();

    if ($details) {
      return json_decode($details->body);
    }

    return null;
  }

  private function documentRepositoryTable($type)
  {
      return 'culturefeed_udb3_' . $type . '_document_repository';
  }
}
