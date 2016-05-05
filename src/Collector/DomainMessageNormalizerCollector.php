<?php

namespace Drupal\culturefeed_udb3\Collector;

/**
 * Class DomainMessageNormalizerCollector.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class DomainMessageNormalizerCollector {

  /**
   * The normalizers.
   *
   * @var \CultuurNet\BroadwayAMQP\Normalizer\DomainMessageNormalizerInterface[]
   */
  protected $normalizers;

  /**
   * DomainMessageNormalizerCollector constructor.
   *
   * @param \CultuurNet\BroadwayAMQP\Normalizer\DomainMessageNormalizerInterface[] $normalizers
   *   The normalizers.
   */
  public function __construct(array $normalizers = array()) {
    $this->normalizers = $normalizers;
  }

  /**
   * Get the normalizers.
   *
   * @return \CultuurNet\BroadwayAMQP\Normalizer\DomainMessageNormalizerInterface[]
   *   The normalizers.
   */
  public function getNormalizers() {
    return $this->normalizers;
  }

}
