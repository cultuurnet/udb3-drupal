<?php

/**
 * @file
 * Contains \Drupal\culturefeed_udb3\CulturefeedUdb3ServiceProvider.
 */

namespace Drupal\culturefeed_udb3;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\Core\DependencyInjection\YamlFileLoader;

/**
 * Class CulturefeedUdb3ServiceProvider.
 *
 * @package Drupal\culturefeed_udb3
 */
class CulturefeedUdb3ServiceProvider extends ServiceProviderBase {

  /**
   * Registers services to the container.
   *
   * @param ContainerBuilder $container
   *   The ContainerBuilder to register services to.
   */
  public function register(ContainerBuilder $container) {
    $container->addCompilerPass(new RegisterEventBusSubscribersPass());

    // Load separated service files.
    $modules = $container->getParameter('container.modules');
    $pathname = isset($modules['culturefeed_udb3']['pathname']) ? $modules['culturefeed_udb3']['pathname'] : NULL;
    if ($pathname) {
      $path = dirname($pathname) . '/services';
      $yaml_loader = new YamlFileLoader($container);
      foreach (new \DirectoryIterator($path) as $component) {
        /* @var \DirectoryIterator $component */
        if (!$component->isDot() && !$component->isDir()) {
          $yaml_loader->load($component->getPathName());
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('redirect_response_subscriber');
    $definition->setClass('Drupal\culturefeed_udb3\CulturefeedUdb3RedirectResponseSubscriber');
  }

}
