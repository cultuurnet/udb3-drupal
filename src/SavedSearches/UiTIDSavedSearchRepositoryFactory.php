<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\UiTIDSavedSearchesFactory.
 */

namespace Drupal\culturefeed_udb3\SavedSearches;

use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UDB3\SavedSearches\SavedSearchesServiceFactoryInterface;
use CultuurNet\UDB3\SavedSearches\UiTIDSavedSearchRepository;
use Drupal\culturefeed\UserCredentials;

/**
 * Class UiTIDSavedSearchesFactory.
 *
 * @package Drupal\culturefeed_udb3
 */
class UiTIDSavedSearchRepositoryFactory {

  /**
   * @var UserCredentials
   */
  protected $userCredentials;

  /**
   * @var SavedSearchesServiceFactoryInterface
   */
  protected $serviceFactory;

  /**
   * @param SavedSearchesServiceFactoryInterface $serviceFactory
   * @param UserCredentials $userCredentials
   */
  public function __construct(
    SavedSearchesServiceFactoryInterface $serviceFactory,
    UserCredentials $userCredentials
  ) {
    $this->serviceFactory = $serviceFactory;
    $this->userCredentials = $userCredentials;
  }

  /**
   * Get an instance of the UiTID saved search repository
   *
   * @return UiTIDSavedSearchRepository
   */
  public function get() {
    $tokenCredentials = new TokenCredentials(
      $this->userCredentials->getToken(),
      $this->userCredentials->getSecret()
    );

    $savedSearches = $this->serviceFactory->withTokenCredentials($tokenCredentials);
    return new UiTIDSavedSearchRepository($savedSearches);
  }

}
