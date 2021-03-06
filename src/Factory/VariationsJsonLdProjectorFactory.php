<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Iri\CallableIriGenerator;
use CultuurNet\UDB3\Offer\LocalOfferReadingService;
use CultuurNet\UDB3\Variations\ReadModel\JSONLD\Projector;
use Drupal\Core\Config\ConfigFactory;
use Drupal\culturefeed_udb3\Repository\CacheDocumentRepository;
use Drupal\culturefeed_udb3\Repository\VariationSearchRepository;

/**
 * Class VariationsJsonLdProjectorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class VariationsJsonLdProjectorFactory {

  /**
   * The local offer reading service.
   *
   * @var \CultuurNet\UDB3\Offer\LocalOfferReadingService
   */
  protected $localOfferReadingService;

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
   * @param \CultuurNet\UDB3\Offer\LocalOfferReadingService $local_offer_reading_service
   *   The event json ld repository.
   * @param \Drupal\culturefeed_udb3\Repository\VariationSearchRepository $variations_search_repository
   *   The variations search repository.
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(
    CacheDocumentRepository $variations_json_ld_repository,
    LocalOfferReadingService $local_offer_reading_service,
    VariationSearchRepository $variations_search_repository,
    ConfigFactory $config
  ) {
    $this->variationsJsonLdRepository = $variations_json_ld_repository;
    $this->localOfferReadingService = $local_offer_reading_service;
    $this->variationsSearchRepository = $variations_search_repository;
    $this->url = $config->get('culturefeed_udb3.settings')->get('url');

  }

  /**
   * Get the variations json ld projector.
   *
   * @return \CultuurNet\UDB3\Variations\ReadModel\JSONLD\Projector
   *   The json ld projector.
   */
  public function get() {

    $url = $this->url;
    $iri_generator = new CallableIriGenerator(
      function ($id) use ($url) {
        return $url . '/udb3/api/1.0/variations/' . $id;
      }
    );

    return new Projector(
      $this->variationsJsonLdRepository,
      $this->localOfferReadingService,
      $this->variationsSearchRepository,
      $iri_generator
    );

  }

}
