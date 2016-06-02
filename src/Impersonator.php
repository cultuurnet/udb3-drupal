<?php

namespace Drupal\culturefeed_udb3;

use Broadway\Domain\Metadata;
use CultuurNet\Auth\TokenCredentials;
use Lcobucci\JWT\Token as Jwt;

class Impersonator {

  /**
   * The json web token.
   *
   * @var Jwt|null
   */
  private $jwt;

  /**
   * The token credentials.
   *
   * @var TokenCredentials|null
   */
  private $tokenCredentials;

  /**
   * The user.
   *
   * @var \CultureFeed_User
   */
  private $user;


  /**
   * Impersonator constructor.
   */
  public function __construct() {
    $this->user = null;
    $this->tokenCredentials = null;
  }

  /**
   * Get the user.
   *
   * @return \CultureFeed_User|null
   *   The user.
   */
  public function getUser() {
    return $this->user;
  }

  /**
   * Get the token credentials.
   *
   * @return TokenCredentials|null
   *   The token credentials.
   */
  public function getTokenCredentials() {
    return $this->tokenCredentials;
  }

  /**
   * Get the json web token.
   *
   * @return Jwt|null
   *   The json web token.
   */
  public function getJwt() {
    return $this->jwt;
  }

  /**
   * Impersonate.
   *
   * @param Metadata $metadata
   *   The metadata.
   */
  public function impersonate(Metadata $metadata) {

    $metadata = $metadata->serialize();

    $this->user = new \CultureFeed_User();
    $this->user->id = $metadata['user_id'];
    $this->user->nick = $metadata['user_nick'];

    // There might still be queued commands without this metadata because
    // it was added later.
    $this->user->mbox = isset($metadata['user_email']) ? $metadata['user_email'] : null;
    $this->jwt = isset($metadata['auth_jwt']) ? $metadata['auth_jwt'] : null;

    $this->tokenCredentials = $metadata['uitid_token_credentials'];

  }

}
