<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Controller\SavedSearchesController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use Broadway\CommandHandling\CommandBusInterface;
use CultuurNet\UDB3\SavedSearches\Command\SubscribeToSavedSearchJSONDeserializer;
use CultuurNet\UDB3\SavedSearches\Command\UnsubscribeFromSavedSearch;
use CultuurNet\UDB3\SavedSearches\ReadModel\SavedSearchRepositoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CultureFeed_User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use ValueObjects\String\String;

/**
 * Class SavedSearchesController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class SavedSearchesController extends ControllerBase {

  /**
   * The culturefeed user service.
   *
   * @var CultureFeed_User;
   */
  protected $user;

  /**
   * @var CommandBusInterface
   */
  protected $commandBus;

  /**
   * @var SavedSearchRepositoryInterface
   */
  protected $savedSearches;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('culturefeed.current_user'),
      $container->get('culturefeed_udb3.event_command_bus'),
      $container->get('culturefeed_udb3.saved_searches_repository')
    );
  }

  /**
   * Constructs a RestController.
   *
   * @param CultureFeed_User $user
   *   A culturefeed user object.
   * @param CommandBusInterface $commandBus
   * @param SavedSearchRepositoryInterface $savedSearchRepository
   */
  public function __construct(
    CultureFeed_User $user,
    CommandBusInterface $commandBus,
    SavedSearchRepositoryInterface $savedSearchRepository
  ) {
    $this->user = $user;
    $this->commandBus = $commandBus;
    $this->savedSearches = $savedSearchRepository;
  }

  /**
   * Create a saved search.
   *
   * @param Request $request
   *
   * @return JsonResponse
   *   The event history as JSON.
   */
  public function createSavedSearch(Request $request) {
    $userId = new String($this->user->id);
    $deserializer = new SubscribeToSavedSearchJSONDeserializer($userId);
    $data = new String($request->getContent());

    $command = $deserializer->deserialize($data);
    $commandId = $this->commandBus->dispatch($command);

    return JsonResponse::create(
      ['commandId' => $commandId]
    );
  }

  /**
   * Delete a saved search.
   *
   * @param $id
   *
   * @return JsonResponse
   *   The event history as JSON.
   */
  public function deleteSavedSearch($id) {
    $userId = new String($this->user->id);
    $searchId = new String($id);

    $command = new UnsubscribeFromSavedSearch($userId, $searchId);
    $commandId = $this->commandBus->dispatch($command);

    return JsonResponse::create(
      ['commandId' => $commandId]
    );
}

  /**
   * List all the saved searches for the active user
   *
   * @return mixed
   */
  public function listSavedSearches() {
    return JsonResponse::create(
      $this->savedSearches->ownedByCurrentUser()
    );
  }

}
