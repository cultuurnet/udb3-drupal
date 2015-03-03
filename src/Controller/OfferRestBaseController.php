<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\OfferCrudBaseController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultuurNet\UDB3\Event\Event;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class for offer reset callbacks.
 */
class OfferRestBaseController extends ControllerBase {

  protected $editor;

  /**
   * Update the description property.
   *
   * @param Request $request
   * @param type $cdbid
   * @return JsonResponse
   */
  public function updateDescription(Request $request, $cdbid) {

    $response = new JsonResponse();
    $body_content = json_decode($request->getContent());

    if (!$body_content->description) {
      return new JsonResponse(['error' => "description required"], 400);
    }

    try {

      $command_id = $this->editor->updateDescription(
        $cdbid,
        $body_content->description
      );

      $response->setData(['commandId' => $command_id]);
    } catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Update the typicalAgeRange property.
   *
   * @param Request $request
   * @param type $cdbid
   * @return JsonResponse
   */
  public function updateTypicalAgeRange(Request $request, $cdbid) {

    $body_content = json_decode($request->getContent());
    if (empty($body_content->typicalAgeRange)) {
      return new JsonResponse(['error' => "typicalAgeRange required"], 400);
    }

    $response = new JsonResponse();
    try {
      $command_id = $this->editor->updateTypicalAgeRange($cdbid, $body_content->typicalAgeRange);
      $response->setData(['commandId' => $command_id]);
    }
    catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Update the organizer property.
   *
   * @param Request $request
   * @param type $cdbid
   * @return JsonResponse
   */
  public function updateOrganizer(Request $request, $cdbid) {

    $body_content = json_decode($request->getContent());
    if (empty($body_content->organizer)) {
      return new JsonResponse(['error' => "organizer required"], 400);
    }

    $response = new JsonResponse();
    try {
      $command_id = $this->editor->updateOrganizer($cdbid, $body_content->organizer);
      $response->setData(['commandId' => $command_id]);
    }
    catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

}
