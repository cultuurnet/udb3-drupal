<?php

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\UDB3\Symfony\JsonLdResponse;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

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
   * Logs the current user out.
   *
   * @return Response
   *   A response.
   */
  public function logout() {
    user_logout();
    return new Response();
  }

}
