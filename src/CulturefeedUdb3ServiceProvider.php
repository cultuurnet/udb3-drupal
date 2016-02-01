<?php
/**
 * @file
 */

namespace Drupal\culturefeed_udb3;


use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Drupal\Core\DependencyInjection\YamlFileLoader;

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

        // Load separated service files.
        $yaml_loader = new YamlFileLoader($container);
        $path = __DIR__ . DIRECTORY_SEPARATOR . '..';
        $yaml_loader->load($path . '/culturefeed_udb3.services.general.yml');
        $yaml_loader->load($path . '/culturefeed_udb3.services.search.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function alter(ContainerBuilder $container) {
        $definition = $container->getDefinition('redirect_response_subscriber');
        $definition->setClass('Drupal\culturefeed_udb3\CulturefeedUdb3RedirectResponseSubscriber');
    }

}
