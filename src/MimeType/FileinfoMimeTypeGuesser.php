<?php

namespace Drupal\culturefeed_udb3\MimeType;

use \Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser as SymfonyFileinfoMimeTypeGuesser;

/**
 * Class FileinfoMimeTypeGuesser.
 *
 * @package Drupal\culturefeed_udb3\MimeType
 */
class FileinfoMimeTypeGuesser extends SymfonyFileinfoMimeTypeGuesser {

  /**
   * {@inheritdoc}
   */
  public function guess($path) {

    // Culturefeed udb3 needs a file info mime type guesser but doesn't work
    // on files uploaded by Drupal.  In that case we catch the error and
    // return null so the drupal extension mime type gueeser can take over.
    if (!is_file($path)) {
      return NULL;
    }

    else {
      return parent::guess($path);
    }

  }

}
