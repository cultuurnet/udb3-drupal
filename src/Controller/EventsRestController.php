<?php

/**
 * @file
 * Contains Drupal\culturefeed\Controller\EventsRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CultuurNet\UDB3\Search\SearchServiceInterface;
use CultuurNet\UDB3\Event\EventLabellerServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use CultuurNet\UDB3\UsedLabelsMemory\DefaultUsedLabelsMemoryService;
use CultureFeed_User;
use CultuurNet\UDB3\Symfony\JsonLdResponse;
use CultuurNet\UDB3\Label;

/**
 * Class EventsRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class EventsRestController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('culturefeed_udb3.search_service'),
      $container->get('culturefeed_udb3.event.labeller'),
      $container->get('culturefeed_udb3.event.used_labels_memory'),
      $container->get('culturefeed.current_user')
    );
  }

  /**
   * Constructs a RestController.
   *
   * @param SearchServiceInterface $search_service
   *   The search service.
   * @param EventLabellerServiceInterface $event_labeller
   *   The event labeller.
   * @param DefaultUsedLabelsMemoryService $used_labels_memory
   *   The event labeller.
   * @param CultureFeed_User $user
   *   The event labeller.
   */
  public function __construct(
    SearchServiceInterface $search_service,
    EventLabellerServiceInterface $event_labeller,
    DefaultUsedLabelsMemoryService $used_labels_memory,
    CultureFeed_User $user
  ) {
    $this->searchService = $search_service;
    $this->eventLabeller = $event_labeller;
    $this->usedLabelsMemory = $used_labels_memory;
    $this->user = $user;
  }

  /**
   * Label culturefeed events.
   *
   * @param Request $request
   *   The request.
   *
   * @return JsonLdResponse
   *   A json response.
   */
  public function labelEvents(Request $request) {

    $response = JsonLdResponse::create();

    try {
      $body_content = json_decode($request->getContent());
      $label = new Label($body_content->label);
      $event_ids = $body_content->events;

      $command_id = $this->eventLabeller->labelEventsById($event_ids, $label);

      $user = $this->user;
      $this->usedLabelsMemory->rememberLabelUsed(
        $user->id,
        $label
      );

      $response->setData(['commandId' => $command_id]);

    }
    catch (\Exception $e) {

      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);

    };

    return $response;

  }

  /**
   * Label culturefeed events.
   *
   * @param Request $request
   *   The request.
   *
   * @return JsonLdResponse
   *   A json response.
   */
  public function labelQuery(Request $request) {

    $response = JsonLdResponse::create();

    try {
      $body_content = json_decode($request->getContent());
      $label = new Label($body_content->label);
      $query = $body_content->query;
      if (!$query) {
        return new JsonLDResponse(['error' => "query required"], 400);
      }

      $command_id = $this->eventLabeller->labelQuery($query, $label);

      $user = $this->user;
      $this->usedLabelsMemory->rememberLabelUsed(
        $user->id,
        $label
      );

      $response->setData(['commandId' => $command_id]);

    }
    catch (\Exception $e) {

      $response->setStatusCode(400);
      $response->setData(['error' => $e->getMessage()]);

    };

    return $response;

  }

}
