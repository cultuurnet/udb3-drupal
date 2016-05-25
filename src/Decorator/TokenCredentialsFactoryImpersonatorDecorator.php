<?php

namespace Drupal\culturefeed_udb3\Decorator;

use Drupal\culturefeed\TokenCredentialsFactoryInterface;
use Drupal\culturefeed_udb3\Impersonator;

/**
 * Class TokenCredentialsFactoryImpersonatorDecorator.
 *
 * @package Drupal\culturefeed_udb3\Decorator
 */
class TokenCredentialsFactoryImpersonatorDecorator implements TokenCredentialsFactoryInterface {

  /**
   * The impersonator.
   *
   * @var \Drupal\culturefeed_udb3\Impersonator
   */
  protected $impersonator;

  /**
   * The token credentials factory.
   *
   * @var \Drupal\culturefeed\TokenCredentialsFactoryInterface
   */
  protected $tokenCredentialsFactory;

  /**
   * TokenCredentialsFactoryImpersonatorDecorator constructor.
   *
   * @param \Drupal\culturefeed\TokenCredentialsFactoryInterface $token_credentials_factory
   *   The token credentials factory.
   * @param \Drupal\culturefeed_udb3\Impersonator $impersonator
   *   The impersonator.
   */
  public function __construct(TokenCredentialsFactoryInterface $token_credentials_factory, Impersonator $impersonator) {
    $this->tokenCredentialsFactory = $token_credentials_factory;
    $this->impersonator = $impersonator;
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    if ($this->impersonator->getTokenCredentials()) {
      return $this->impersonator->getTokenCredentials();
    }

    return $this->tokenCredentialsFactory->get();

  }

}
