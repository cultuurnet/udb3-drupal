<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\OfferCrudBaseController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultuurNet\UDB3\BookingInfo;
use CultuurNet\UDB3\Calendar;
use CultuurNet\UDB3\ContactPoint;
use CultuurNet\UDB3\Timestamp;
use Drupal\Core\Controller\ControllerBase;
use Exception;
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
   * @param string $cdbid
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
    } catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Update the typicalAgeRange property.
   *
   * @param Request $request
   * @param string $cdbid
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
    catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Update the organizer property.
   *
   * @param Request $request
   * @param string $cdbid
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
    catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Delete the given organizer.
   *
   * @param string $cdbid
   * @param string $organizerId
   * @return JsonResponse
   */
  public function deleteOrganizer($cdbid, $organizerId) {

    $response = new JsonResponse();
    try {
      $command_id = $this->editor->deleteOrganizer($cdbid, $organizerId);
      $response->setData(['commandId' => $command_id]);
    }
    catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Update the contactPoint.
   *
   * @param Request $request
   * @param string $cdbid
   * @return JsonResponse
   */
  public function updateContactPoint(Request $request, $cdbid) {

    $body_content = json_decode($request->getContent());
    if (empty($body_content->contactPoint) || !isset($body_content->contactPoint->url) || !isset($body_content->contactPoint->email) || !isset($body_content->contactPoint->phone)) {
      return new JsonResponse(['error' => "contactPoint and his properties required"], 400);
    }

    $response = new JsonResponse();
    try {
      $command_id = $this->editor->updateContactPoint($cdbid, new ContactPoint($body_content->contactPoint->phone, $body_content->contactPoint->email, $body_content->contactPoint->url));
      $response->setData(['commandId' => $command_id]);
    }
    catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }
  
  /**
   * Update the bookingInfo.
   *
   * @param Request $request
   * @param string $cdbid
   * @return JsonResponse
   */
  public function updateBookingInfo(Request $request, $cdbid) {

    $body_content = json_decode($request->getContent());
    if (empty($body_content->bookingInfo)) {
      return new JsonResponse(['error' => "bookingInfo required"], 400);
    }

    $response = new JsonResponse();
    try {
      $data = $body_content->bookingInfo;
      $bookingInfo = new BookingInfo($data->url, $data->urlLabel, $data->phone, $data->email, 
        $data->availabilityStarts, $data->availabilityEnds, $data->availabilityStarts, $data->availabilityStarts);
      $command_id = $this->editor->updateBookingInfo($cdbid, $bookingInfo);
      $response->setData(['commandId' => $command_id]);
    }
    catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Update the facilities.
   *
   * @param Request $request
   * @param string $cdbid
   * @return JsonResponse
   */
  public function updateFacilities(Request $request, $cdbid) {

    return new JsonResponse(['error' => "facilities required"], 200);

    $body_content = json_decode($request->getContent());
    if (empty($body_content->facilities)) {
      return new JsonResponse(['error' => "facilities required"], 400);
    }

    $response = new JsonResponse();
    try {
      $command_id = $this->editor->updateFacilities($cdbid, $body_content->facilities);
      $response->setData(['commandId' => $command_id]);
    }
    catch (Exception $e) {
      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);
    }

    return $response;

  }

  /**
   * Init the calendar object to use for a create (event / place)
   */
  protected function initCalendarForCreate($body_content) {

      // Cleanup empty timestamps.
      $timestamps = array();
      if (!empty($body_content->timestamps)) {
        foreach ($body_content->timestamps as $timestamp) {
          if (!empty($timestamp->date)) {
            $date = date('Y-m-d', strtotime($timestamp->date));
            if (!empty($timestamp->showStartHour)) {
              $startDate = $date . 'T' . $timestamp->startHour . ':00';
            }
            else {
              $startDate = $date . 'T00:00:00';
            }

            if (!empty($timestamp->showEndHour)) {
              $endDate = $date . 'T' . $timestamp->endHour . ':00';
            }
            else {
              $endDate = $date . 'T00:00:00';
            }
            $timestamps[strtotime($startDate)] = new Timestamp($startDate, $endDate);
          }
        }
        ksort($timestamps);
      }

      $startDate = !empty($body_content->startDate) ? $body_content->startDate : '';
      $endDate = !empty($body_content->endDate) ? $body_content->endDate : '';

      // For single calendar type, check if it should be multiple
      // Also calculate the correct startDate and endDate for the calendar object.
      $calendarType = !empty($body_content->calendarType) ? $body_content->calendarType : 'permanent';
      if ($calendarType == Calendar::SINGLE && count($timestamps) == 1) {

        // 1 timestamp = no timestamps needed. Copy start and enddate.
        $firstTimestamp = current($timestamps);
        $startDate = $firstTimestamp->getStartDate();
        $endDate = $firstTimestamp->getEndDate();
        $timestamps = array();
      }
      elseif ($calendarType == Calendar::SINGLE && count($timestamps) > 1) {

        // Multiple timestamps, startDate = first date, endDate = last date.
        $calendarType = Calendar::MULTIPLE;
        $firstTimestamp = current($timestamps);
        $lastTimestamp = end($timestamps);
        $startDate = $firstTimestamp->getStartDate();
        $endDate = $lastTimestamp->getEndDate();

      }

      // Remove empty opening hours.
      $openingHours = array();
      if (!empty($body_content->openingHours)) {
        $openingHours = $body_content->openingHours;
        foreach ($openingHours as $key => $openingHour) {
          if (empty($openingHour->daysOfWeek) || empty($openingHour->opens) || empty($openingHour->closes)) {
            unset($openingHours[$key]);
          }
        }
      }

      return new Calendar($calendarType, $startDate, $endDate, $timestamps, $openingHours);
  }

}
