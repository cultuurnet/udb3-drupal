<?php

namespace Drupal\culturefeed_udb3\Factory;

use CultuurNet\SymfonySecurityJwt\Authentication\JwtUserToken;
use CultuurNet\UDB3\Http\JwtPsr7RequestAuthorizer;
use CultuurNet\UDB3\Jwt\JwtDecoderServiceInterface;
use Drupal\culturefeed_jwt\Factory\JwtTokenFactoryInterface;
use Lcobucci\JWT\Token;
use ValueObjects\String\String as StringLiteral;

/**
 * Class JwtPsr7RequestAuthorizerFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class JwtPsr7RequestAuthorizerFactory {

  /**
   * The jwt decoder service.
   *
   * @var \CultuurNet\UDB3\Jwt\JwtDecoderServiceInterface
   */
  protected $jwtDecoderService;

  /**
   * The jwt token factory.
   *
   * @var \Drupal\culturefeed_jwt\Factory\JwtTokenFactoryInterface
   */
  protected $jwtTokenFactory;

  /**
   * JwtPsr7RequestAuthorizerFactory constructor.
   *
   * @param \CultuurNet\UDB3\Jwt\JwtDecoderServiceInterface
   *   The jwt decoder service.
   * @param \Drupal\culturefeed_jwt\Factory\JwtTokenFactoryInterface $jwt_token_factory
   *   The jwt token factory.
   */
  public function __construct(JwtDecoderServiceInterface $jwt_decoder_service, JwtTokenFactoryInterface $jwt_token_factory) {
    $this->jwtDecoderService = $jwt_decoder_service;
    $this->jwtTokenFactory = $jwt_token_factory;
  }

  /**
   * Get the jwt psr7 request authorizer.
   *
   * @return \CultuurNet\UDB3\Http\JwtPsr7RequestAuthorizer
   *   The jwt psr7 request authorizer.
   */
  public function get() {

    $token_string = $this->jwtTokenFactory->get();
    if ($token_string) {
      $token = $this->jwtDecoderService->parse(new StringLiteral($token_string));
    }
    else {
      $token = new Token();
    }
    return new JwtPsr7RequestAuthorizer($token);

  }

}
