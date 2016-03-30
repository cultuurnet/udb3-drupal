<?php

namespace Drupal\culturefeed_udb3\Controller;

use CultureFeed_User;
use CultuurNet\Hydra\PagedCollection;
use CultuurNet\Hydra\Symfony\PageUrlGenerator;
use CultuurNet\UDB3\Dashboard\DashboardItemLookupServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;
use ValueObjects\Number\Natural;
use ValueObjects\Web\Domain;

/**
 * Class DashboardRestController.
 *
 * @package Drupal\culturefeed_udb3\Controller
 */
class DashboardRestController {

  /**
   * The culturefeed user.
   *
   * @var \CultureFeed_User
   */
  private $currentUser;

  /**
   * The domain.
   *
   * @var \ValueObjects\Web\Domain
   */
  protected $domain;

  /**
   * The item lookup service.
   *
   * @var \CultuurNet\UDB3\Dashboard\DashboardItemLookupServiceInterface
   */
  private $itemLookupService;

  /**
   * The url generator.
   *
   * @var \Symfony\Component\Routing\Generator\UrlGenerator
   */
  private $urlGenerator;

  /**
   * DashboardRestController constructor.
   *
   * @param DashboardItemLookupServiceInterface $item_lookup_service
   *   The item lookup service.
   * @param CultureFeed_User $current_user
   *   The culturefeed user.
   * @param \ValueObjects\Web\Domain $domain
   *   The domain.
   * @param \Symfony\Component\Routing\Generator\UrlGenerator $url_generator
   *   The url generator.
   */
  public function __construct(
    DashboardItemLookupServiceInterface $item_lookup_service,
    CultureFeed_User $current_user,
    Domain $domain,
    UrlGenerator $url_generator
  ) {
    $this->itemLookupService = $item_lookup_service;
    $this->currentUser = $current_user;
    $this->domain = $domain;
    $this->urlGenerator = $url_generator;
  }

  /**
   * Get the items owned by current user for a domain.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response.
   */
  public function itemsOwnedByCurrentUser(Request $request) {

    $pageNumber = intval($request->query->get('page', 1));
    $limit = 50;

    $items = $this->itemLookupService->findByUserForDomain(
      $this->currentUser,
      Natural::fromNative($limit),
      Natural::fromNative(--$pageNumber * $limit),
      $this->domain
    );

    $pageUrlFactory = new PageUrlGenerator(
      $request->query,
      $this->urlGenerator,
      'dashboard-items',
      'page'
    );

    return JsonResponse::create(
      new PagedCollection(
        $pageNumber,
        $limit,
        $items->getItems(),
        $items->getTotalItems()->toNative(),
        $pageUrlFactory
      )
    );

  }

}
