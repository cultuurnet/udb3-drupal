<?php

namespace Drupal\culturefeed_udb3;

use CultuurNet\UDB3\Iri\IriGeneratorInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;

/**
 * Class IriGenerator.
 *
 * @package Drupal\culturefeed_udb3
 */
class IriGenerator implements IriGeneratorInterface {

  /**
   * Name of the route to show a single event.
   *
   * @var string
   */
  protected $eventRouteName;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * Drupal's UrlGeneratorInterface.
   *
   * @var UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new IriGenerator for use with Drupal's URLGeneratorInterface.
   *
   * @param UrlGeneratorInterface $url_generator
   *   The url generator.
   * @param string $event_route_name
   *   The event route name.
   */
  public function __construct(
    UrlGeneratorInterface $url_generator,
    $event_route_name = 'culturefeed_udb3.event'
  ) {
    $this->urlGenerator = $url_generator;
    $this->eventRouteName = $event_route_name;
  }

  /**
   * {@inheritdoc}
   */
  public function iri($item) {

    // @TODO
    // Implement cleaner approach once https://www.drupal.org/node/2616164 is
    // in.  For the same reason the language manager isn't injected, as there
    // are multiple services using this class and it's only temporary.
    $language = \Drupal::languageManager()->getLanguage(\Drupal\Core\Language\LanguageInterface::LANGCODE_NOT_APPLICABLE);
    return $this->urlGenerator->generateFromRoute(
      $this->eventRouteName,
      array(
        'cdbid' => $item,
      ),
      array(
        'absolute' => TRUE,
        'language' => $language,
      )
    );
  }

}
