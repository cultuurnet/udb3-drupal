<?php

/**
 * @file
 * Contains Drupal\culturefeed\Controller\UserRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CultureFeed_User;
use CultuurNet\UDB3\UsedLabelsMemory\UsedLabelsMemoryServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use CultuurNet\UDB3\Symfony\JsonLdResponse;

/**
 * Class UserRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class UserRestController extends ControllerBase {

  /**
   * The culturefeed user service.
   *
   * @var CultureFeed_User;
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('culturefeed.current_user'),
      $container->get('culturefeed_udb3.used_labels_memory')
    );
  }

  /**
   * Constructs a RestController.
   *
   * @param CultureFeed_User $user
   *   A culturefeed user object.
   */
  public function __construct(CultureFeed_User $user, UsedLabelsMemoryServiceInterface $memory) {
    $this->user = $user;
    $this->memory = $memory;
  }

  /**
   * Returns culturefeed user data.
   *
   * @return JsonLdResponse
   *   A json response.
   */
  public function info() {
    // Remove circular dependencies from the CultureFeed_User instance,
    // otherwise encoding it as JSON will fail.
    $user = clone $this->user;
    unset($user->following);

    $response = JsonLdResponse::create()
      ->setData($user)
      ->setPublic()
      ->setClientTtl(60 * 30)
      ->setTtl(60 * 5);

    return $response;

  }

  /**
   * Returns udb3 labels.
   *
   * @return JsonLdResponse
   *   A json response.
   */
  public function labels() {

    $memory = $this->memory;
    $user = $this->user;
    $memory = $memory->getMemory($user->id);

    $response = JsonResponse::create($memory);

    return $response;

  }

}
