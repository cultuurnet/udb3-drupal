<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Iri\CallableIriGenerator;
use CultuurNet\UDB3\ReadModel\Index\EntityIriGeneratorFactoryInterface;
use CultuurNet\UDB3\ReadModel\Index\EntityType;
use Drupal\Core\Config\ConfigFactory;

/**
 * Class EntityIriGeneratorFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class EntityIriGeneratorFactory implements EntityIriGeneratorFactoryInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * EntityIriGeneratorFactoryFactory constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $config
   *   The config factory.
   */
  public function __construct(ConfigFactory $config) {
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * Return the entity iri generator factory.
   *
   * @param \CultuurNet\UDB3\ReadModel\Index\EntityType $entity_type
   *   The entity type.
   *
   * @return \CultuurNet\UDB3\ReadModel\Index\EntityIriGeneratorFactory
   *   The entity iri generator factory.
   */
  public function forEntityType(EntityType $entity_type) {

    $url = $this->config->get('url');
    return new CallableIriGenerator(
      function ($cdbid) use ($url, $entity_type) {
        return $url . '/udb3/api/1.0/' . $entity_type . '/' . $cdbid;
      }
    );

  }

}
