<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\BroadwayAMQP\Normalizer\CombinedDomainMessageNormalizer;
use Drupal\culturefeed_udb3\Collector\DomainMessageNormalizerCollector;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CombinedDomainMessageNormalizerFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class CombinedDomainMessageNormalizerFactory {

  /**
   * The domain message normalizer collector.
   *
   * @var \Drupal\culturefeed_udb3\Collector\DomainMessageNormalizerCollector
   */
  protected $collector;

  /**
   * The service container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * CombinedDomainMessageNormalizerFactory constructor.
   *
   * @param \Drupal\culturefeed_udb3\Collector\DomainMessageNormalizerCollector $collector
   *   The domain message normalizer collector.
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   */
  public function __construct(
    DomainMessageNormalizerCollector $collector,
    ContainerInterface $container
  ) {
    $this->collector = $collector;
    $this->container = $container;
  }

  /**
   * Get the normalizers.
   *
   * @return \CultuurNet\BroadwayAMQP\Normalizer\CombinedDomainMessageNormalizer
   *   The combined domain message normalizer.
   */
  public function get() {

    $combiner = new CombinedDomainMessageNormalizer();
    foreach ($this->collector->getNormalizers() as $normalizer) {
      /* @var \CultuurNet\BroadwayAMQP\Normalizer\DomainMessageNormalizerInterface $normalizer */
      $normalizer = $this->container->get($normalizer);
      $combiner = $combiner->withNormalizer($normalizer);
    }
    return $combiner;

  }

}
