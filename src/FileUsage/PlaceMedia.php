<?php

namespace Drupal\culturefeed_udb3\FileUsage;

use CultuurNet\UDB3\Place\Events\ImageRemoved;
use CultuurNet\UDB3\Place\Events\ImageAdded;

/**
 * Class PlaceMedia.
 *
 * @package Drupal\culturefeed_udb3\FileUsage
 */
class PlaceMedia extends OfferMedia {

  /**
   * Add file to Drupal file system when added.
   *
   * @param \CultuurNet\UDB3\Place\Events\ImageAdded $image_added
   *   The image added event.
   */
  protected function applyImageAdded(ImageAdded $image_added) {
    parent::offerImageAdded($image_added);
  }

  /**
   * Cleanup file entities after image delete.
   *
   * @param \CultuurNet\UDB3\Place\Events\ImageRemoved $image_removed
   *   The image removed event.
   */
  protected function applyImageRemoved(ImageRemoved $image_removed) {
    parent::offerImageRemoved($image_removed);
  }

}
