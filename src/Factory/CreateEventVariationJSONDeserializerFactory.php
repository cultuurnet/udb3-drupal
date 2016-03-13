<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\EventServiceInterface;
use CultuurNet\UDB3\Variations\Command\CreateEventVariationJSONDeserializer;
use CultuurNet\UDB3\Variations\Model\Properties\DefaultUrlValidator;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class CreateEventVariationJSONDeserializerFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class CreateEventVariationJSONDeserializerFactory {

  /**
   * The event service.
   *
   * @var EventServiceInterface
   */
  protected $eventService;

  /**
   * The event url regex.
   *
   * @var string
   */
  protected $eventUrlRegex;

  /**
   * CreateEventVariationJSONDeserializerFactory constructor.
   *
   * @param EventServiceInterface $event_service
   *   The event service.
   * @param ConfigFactoryInterface $config
   *   The config factory.
   */
  public function __construct(
      EventServiceInterface $event_service,
      ConfigFactoryInterface $config
  ) {
    $this->eventService = $event_service;
    $this->eventUrlRegex = $config->get('culturefeed_udb3.settings')->get('event_url_regex');
  }

  /**
   * Get the command deserializer.
   *
   * @return CreateEventVariationJSONDeserializer
   *   The command deserializer.
   */
  public function get() {
    $deserializer = new CreateEventVariationJSONDeserializer();
    $deserializer->addUrlValidator(
      new DefaultUrlValidator(
        $this->eventUrlRegex,
        $this->eventService
      )
    );
    return $deserializer;
  }

}
