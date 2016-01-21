<?php
/**
 * @file
 */

namespace Drupal\culturefeed_udb3;


use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;

class CulturefeedUdb3ServiceProvider extends ServiceProviderBase
{
    /**
     * Registers services to the container.
     *
     * @param ContainerBuilder $container
     *   The ContainerBuilder to register services to.
     */
    public function register(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterEventBusSubscribersPass());
    }

    /**
     * {@inheritdoc}
     */
    public function alter(ContainerBuilder $container) {
        $definition = $container->getDefinition('redirect_response_subscriber');
        $definition->setClass('Drupal\culturefeed_udb3\CulturefeedUdb3RedirectResponseSubscriber');
    }

}
