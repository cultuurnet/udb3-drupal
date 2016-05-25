<?php

namespace Drupal\culturefeed_udb3\Decorator;

use Drupal\culturefeed_jwt\Factory\JwtTokenFactoryInterface;
use Drupal\culturefeed_udb3\Impersonator;

/**
 * Class JwtStorageTokenFactoryImpersonatorDecorator.
 *
 * @package Drupal\culturefeed_udb3\Decorator
 */
class JwtStorageTokenFactoryImpersonatorDecorator implements JwtTokenFactoryInterface {

  /**
   * The impersonator.
   *
   * @var \Drupal\culturefeed_udb3\Impersonator
   */
  protected $impersonator;

  /**
   * The jwt token factory.
   *
   * @var \Drupal\culturefeed_jwt\Factory\JwtTokenFactoryInterface
   */
  protected $jwtTokenFactory;

  /**
   * JwtStorageTokenFactoryImpersonatorDecorator constructor.
   *
   * @param \Drupal\culturefeed_jwt\Factory\JwtTokenFactoryInterface $jwt_token_factory
   *   The jwt token factory.
   * @param \Drupal\culturefeed_udb3\Impersonator $impersonator
   *   The impersonator.
   */
  public function __construct(JwtTokenFactoryInterface $jwt_token_factory, Impersonator $impersonator) {
    $this->jwtTokenFactory = $jwt_token_factory;
    $this->impersonator = $impersonator;
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    if ($this->impersonator->getJwt()) {
      return (string) $this->impersonator->getJwt();
    }

    return $this->jwtTokenFactory->get();

  }

}
