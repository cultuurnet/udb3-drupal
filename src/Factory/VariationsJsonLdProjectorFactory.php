<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Factory\VariationsJsonLdProjectorFactory
 */

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator;
use CultuurNet\UDB3\Iri\CallableIriGenerator;
use CultuurNet\UDB3\Variations\ReadModel\JSONLD\Projector;
use Drupal\Core\Config\ConfigFactory;
use Drupal\culturefeed_udb3\Repository\CacheDocumentRepository;
use Drupal\culturefeed_udb3\Repository\VariationSearchRepository;

class VariationsJsonLdProjectorFactory {

  /**
   * The event json ld repository.
   *
   * @var \CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator
   */
  protected $eventJsonLdRepository;

  /**
   * The url.
   *
   * @var string
   */
  protected $url;

  /**
   * The variations json ld repository.
   *
   * @var \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository
   */
  protected $variationsJsonLdRepository;

  /**
   * The variations search repository.
   *
   * @var \Drupal\culturefeed_udb3\Repository\VariationSearchRepository
   */
  protected $variationsSearchRepository;

  /**
   * VariationsJsonLdProjectorFactory constructor.
   *
   * @param \Drupal\culturefeed_udb3\Repository\CacheDocumentRepository $variations_json_ld_repository
   *   The variations json ld repository.
   * @param \CultuurNet\UDB3\Event\ReadModel\BroadcastingDocumentRepositoryDecorator $event_json_ld_repository
   *   The event json ld repository.
   * @param \Drupal\culturefeed_udb3\Repository\VariationSearchRepository $variations_search_repository
   *   The variations search repository.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(
    CacheDocumentRepository $variations_json_ld_repository,
    BroadcastingDocumentRepositoryDecorator $event_json_ld_repository,
    VariationSearchRepository $variations_search_repository,
    ConfigFactory $config
  ) {
    $this->variationsJsonLdRepository = $variations_json_ld_repository;
    $this->eventJsonLdRepository = $event_json_ld_repository;
    $this->variationsSearchRepository = $variations_search_repository;
    $this->url = $config->get('culturefeed_udb3.settings')->get('url');

  }

  public function get() {

    $url = $this->url;
    $iriGenerator = new CallableIriGenerator(
      function ($id) use ($url) {
        return $url . '/udb3/api/1.0/variations/' . $id;
      }
    );

    return new Projector(
      $this->variationsJsonLdRepository,
      $this->eventJsonLdRepository,
      $this->variationsSearchRepository,
      $iriGenerator
    );

  }
}