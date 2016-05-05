<?php

namespace Drupal\culturefeed_udb3;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RegisterDomainMessageNormalizerCollectorSubscribersPass.
 *
 * @package Drupal\culturefeed_udb3
 */
class RegisterDomainMessageNormalizerCollectorSubscribersPass implements CompilerPassInterface {

  /**
   * {@inheritdoc}
   */
  public function process(ContainerBuilder $container) {

    $collector_id = 'culturefeed_udb3.domain_message_normalizer_collector';
    if (!$container->hasDefinition($collector_id)) {
      return;
    }

    $definition = $container->getDefinition($collector_id);

    $tagged_services = $container->findTaggedServiceIds($collector_id . '.subscriber');
    $tagged_service_ids = array_keys($tagged_services);

    $definition->addArgument($tagged_service_ids);

  }

}
