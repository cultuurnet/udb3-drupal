<?php
/**
 * Created by PhpStorm.
 * User: hansl
 * Date: 23/05/2016
 * Time: 11:30
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultureFeed_User;
use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;
use CultuurNet\UDB3\EventServiceInterface;
use CultuurNet\UDB3\Iri\IriGeneratorInterface;
use CultuurNet\UDB3\Variations\Model\Properties\OwnerId;
use CultuurNet\UDB3\Variations\Model\Properties\Purpose;
use CultuurNet\UDB3\Variations\ReadModel\Search\Criteria;
use CultuurNet\UDB3\Variations\ReadModel\Search\RepositoryInterface;
use CultuurNet\UDB3\Variations\VariationDecoratedEventService;

class VariationDecoratedEventServiceFactory {

  /**
   * The event iri generator.
   *
   * @var \CultuurNet\UDB3\Iri\IriGeneratorInterface
   */
  protected $eventIriGenerator;

  /**
   * The event service.
   *
   * @var \CultuurNet\UDB3\EventServiceInterface
   */
  protected $eventService;

  /**
   * The culturefeed user.
   *
   * @var \CultureFeed_User
   */
  protected $user;

  /**
   * The variations json ld repository.
   *
   * @var \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface
   */
  protected $variationsJsonLdRepository;

  /**
   * The variations search repository.
   *
   * @var \CultuurNet\UDB3\Variations\ReadModel\Search\RepositoryInterface
   */
  protected $variationsSearchRepository;

  /**
   * VariationDecoratedEventServiceFactory constructor.
   *
   * @param \CultuurNet\UDB3\EventServiceInterface $event_service
   *   The event service.
   * @param \CultuurNet\UDB3\Variations\ReadModel\Search\RepositoryInterface $variations_search_repository
   *   The variations search repository.
   * @param \CultureFeed_User $user
   *   The user.
   * @param \CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface $variations_json_ld_repository
   *   The variations json ld repository.
   * @param \CultuurNet\UDB3\Iri\IriGeneratorInterface $event_iri_generator
   *   The event iri generator.
   */
  public function __construct(
    EventServiceInterface $event_service,
    RepositoryInterface $variations_search_repository,
    CultureFeed_User $user,
    DocumentRepositoryInterface $variations_json_ld_repository,
    IriGeneratorInterface $event_iri_generator
  ) {
    $this->eventService = $event_service;
    $this->variationsSearchRepository = $variations_search_repository;
    $this->user = $user;
    $this->variationsJsonLdRepository = $variations_json_ld_repository;
    $this->eventIriGenerator = $event_iri_generator;
  }

  /**
   * Get the variation decorated event service.
   *
   * @return \CultuurNet\UDB3\Variations\VariationDecoratedEventService
   *   The variation decorated event service.
   */
  public function get() {

    $criteria = (new Criteria())
      ->withPurpose(new Purpose('personal'))
      ->withOwnerId(new OwnerId($this->user->id));

    return new VariationDecoratedEventService(
      $this->eventService,
      $this->variationsSearchRepository,
      $criteria,
      $this->variationsJsonLdRepository,
      $this->eventIriGenerator
    );

  }

}
