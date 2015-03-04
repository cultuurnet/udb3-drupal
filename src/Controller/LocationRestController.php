<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\LocationRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class LocationRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class LocationRestController extends ControllerBase {

  /**
   * Suggest locations based on a search value.
   * @param string $title
   *
   * @return JsonResponse
   *   The response.
   */
  public function suggest($search_string, $postal) {

    $query = db_select('culturefeed_udb3_index', 'i');
    $query->condition('title', '%' . db_like($search_string) . '%', 'LIKE');
    $query->condition('type', 'place');
    $query->condition('zip', $postal);
    $query->range(0, 10);
    $query->fields('i', array('id', 'title'));
    $result = $query->execute();

    $matches = array();
    foreach ($result as $row) {
      $matches[] = $row;
    }

    return JsonResponse::create()
      ->setContent(json_encode($matches))
      ->setPublic()
      ->setClientTtl(60 * 30)
      ->setTtl(60 * 5);

  }
  
}
