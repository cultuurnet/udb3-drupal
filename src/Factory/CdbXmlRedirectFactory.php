<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Symfony\Proxy\Redirect\RedirectInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use ValueObjects\Web\Url;

/**
 * Class CdbXmlRedirectFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class CdbXmlRedirectFactory implements RedirectInterface {

  /**
   * {@inheritdoc}
   */
  public function getRedirectResponse(Url $url) {
    return new RedirectResponse((string) $url);
  }

}
