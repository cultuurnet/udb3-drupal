<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\CityRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CityRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class CityRestController extends ControllerBase {

  /**
   * Suggest locations based on a search value.
   * @param string $search_string
   *
   * @return JsonResponse
   *   The response.
   */
  public function suggest($search_string) {

    $matches = array();
    if ($search_string) {

      $query = db_select('culturefeed_search_cities', 'csc');
      $query->fields('csc', array('cid', 'name', 'zip'));
      $query->condition('cid', '%' . db_like($search_string) . '%', 'LIKE');
      $query->condition('cid', '%(' . db_like($search_string) . '%)', 'NOT LIKE');

      $result = $query->execute();

      foreach ($result as $row) {
        $row->cityId = $row->zip . '_' . $row->name;
        $row->cityLabel = $row->zip . ' ' . $row->name;
        $matches[] = $row;
      }

    }

    return new JsonResponse($matches);

  }
  
}
