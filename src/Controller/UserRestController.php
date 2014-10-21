<?php

/**
 * @file
 * Contains Drupal\culturefeed\Controller\UserRestController.
 */

namespace Drupal\culturefeed_udb3\Controller;

use Drupal\Core\Controller\ControllerBase;
use CultureFeed_User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

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
      $container->get('culturefeed.current_user')
    );
  }

  /**
   * Constructs a RestController.
   *
   * @param CultureFeed_User $user
   *   A culturefeed user object.
   */
  public function __construct(CultureFeed_User $user) {
    $this->user = $user;
  }

  /**
   * Returns culturefeed user data.
   *
   * @return JsonResponse
   *   A json response.
   */
  public function info() {

    $response = JsonResponse::create()
      ->setPublic()
      ->setClientTtl(60 * 1)
      ->setTtl(60 * 5);

    $response
      ->setData($this->user)
      ->setPublic()
      ->setClientTtl(60 * 30)
      ->setTtl(60 * 5);

    $response->headers->set('Content-Type', 'application/ld+json');

    return $response;

  }

}
