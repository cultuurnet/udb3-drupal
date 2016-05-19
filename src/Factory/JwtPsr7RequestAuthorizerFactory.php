<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\UDB3\Http\JwtPsr7RequestAuthorizer;
use Lcobucci\JWT\Token;

/**
 * Class JwtPsr7RequestAuthorizerFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class JwtPsr7RequestAuthorizerFactory {

  /**
   * Get the jwt psr7 request authorizer.
   *
   * @return \CultuurNet\UDB3\Http\JwtPsr7RequestAuthorizer
   *   The jwt psr7 request authorizer.
   */
  public function get() {
    return new JwtPsr7RequestAuthorizer(new Token());
  }

}
