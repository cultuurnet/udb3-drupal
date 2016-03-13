<?php

namespace Drupal\culturefeed_udb3\EventListener;

use Broadway\EventHandling\EventListenerInterface;
use CultuurNet\UDB3\EventHandling\DelegateEventHandlingToSpecificMethodTrait;
use CultuurNet\UDB3\Place\Events\ImageRemoved;
use CultuurNet\UDB3\Place\Events\ImageUpdated;
use Drupal\file\Entity\File;
use Drupal\file\FileUsage\FileUsageInterface;

/**
 * Event listener for places in drupal to cleanup drupal stuff.
 */
class Udb3PlaceEventListener implements EventListenerInterface {

  use DelegateEventHandlingToSpecificMethodTrait;

  /**
   * The file usage interface.
   *
   * @var FileUsageInterface
   */
  protected $fileUsage;

  /**
   * Construct the listener.
   *
   * @param FileUsageInterface $fileUsage
   *   The file usage.
   */
  public function __construct(FileUsageInterface $fileUsage) {
    $this->fileUsage = $fileUsage;
  }

  /**
   * Cleanup file entities after image update.
   *
   * @param \CultuurNet\UDB3\Place\Events\ImageUpdated $image_updated
   *   The image updated event.
   */
  protected function applyImageUpdated(ImageUpdated $image_updated) {

    $internal_id = $image_updated->getMediaObjectId();
    // No need to save if no id given.
    if (empty($internal_id)) {
      return;
    }

    $drupal_file = File::load($internal_id);
    $url = file_create_url($drupal_file->getFileUri());

    // Only delete if old image is not the same.
    if ($image_updated->getMediaObject()->getUrl() !== $url) {
      $file = File::load($internal_id);
      // Delete the usage, cron will  delete the file if this was the only
      // usage.
      if ($file) {
        $this->fileUsage->delete($file, 'culturefeed_udb3');
      }
    }

  }

  /**
   * Cleanup file entities after image delete.
   *
   * @param \CultuurNet\UDB3\Place\Events\ImageRemoved $image_removed
   *   The image removed event.
   */
  protected function applyImageDeleted(ImageRemoved $image_removed) {

    $internal_id = $image_removed->getInternalId();
    if ($image_removed->getInternalId()) {
      $file = File::load($internal_id);
      // Delete the usage, cron will  delete the file if this was the only
      // usage.
      if ($file) {
        $this->fileUsage->delete($file, 'culturefeed_udb3');
      }
    }
  }

}
