<?php

namespace Drupal\culturefeed_udb3\Decorator;

use Drupal\culturefeed\UserFactoryInterface;
use Drupal\culturefeed_udb3\Impersonator;

/**
 * Class UserFactoryImpersonatorDecorator.
 *
 * @package Drupal\culturefeed_udb3\Decorator
 */
class UserFactoryImpersonatorDecorator implements UserFactoryInterface {

  /**
   * The user factory.
   *
   * @var \Drupal\culturefeed\UserFactoryInterface
   */
  protected $userFactory;

  /**
   * The impersonator.
   *
   * @var \Drupal\culturefeed_udb3\Impersonator
   */
  protected $impersonator;

  /**
   * UserFactoryImpersonatorDecorator constructor.
   *
   * @param \Drupal\culturefeed\UserFactoryInterface $user_factory
   *   The user factory.
   * @param \Drupal\culturefeed_udb3\Impersonator $impersonator
   *   The impersonator.
   */
  public function __construct(UserFactoryInterface $user_factory, Impersonator $impersonator) {
    $this->userFactory = $user_factory;
    $this->impersonator = $impersonator;
  }

  /**
   * {@inheritdoc}
   */
  public function get() {

    if ($this->impersonator->getUser()) {
      return $this->impersonator->getUser();
    }

    return $this->userFactory->get();

  }

}
