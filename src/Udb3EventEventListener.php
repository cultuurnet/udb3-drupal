<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Udb3EventListener.
 */

namespace Drupal\culturefeed_udb3;

use Broadway\EventHandling\EventListenerInterface;
use CultuurNet\UDB3\Event\Events\ImageDeleted;
use CultuurNet\UDB3\Event\Events\ImageUpdated;
use CultuurNet\UDB3\EventHandling\DelegateEventHandlingToSpecificMethodTrait;
use Drupal\file\Entity\File;
use Drupal\file\FileUsage\FileUsageInterface;

/**
 * Eventlistener on events in drupal to cleanup drupal stuff.
 */
class Udb3EventEventListener implements EventListenerInterface {

    use DelegateEventHandlingToSpecificMethodTrait;

    /**
     * @var FileUsageInterface
     */
    protected $fileUsage;

    /**
     * Construct the listener.
     */
    public function __construct(FileUsageInterface $fileUsage) {
      $this->fileUsage = $fileUsage;
    }

    /**
     * Cleanup file entities after image update.
     */
    protected function applyImageUpdated(ImageUpdated $imageUpdated) {

      $internalId = $imageUpdated->getMediaObject()->getInternalId();
      // No need to save if no id given.
      if (empty($internalId)) {
        return;
      }

      $drupal_file = File::load($internalId);
      $url = file_create_url($drupal_file->getFileUri());

      // Only delete if old image is not the same.
      if ($imageUpdated->getMediaObject()->getUrl() !== $url) {
          $file = File::load($internalId);
          // Delete the usage, cron will  delete the file if this was the only usage.
          if ($file) {
            $this->fileUsage->delete($file, 'culturefeed_udb3', 'udb3_item', $imageUpdated->getEventId());
          }
      }

    }

    /**
     * Cleanup file entities after image delete
     */
    protected function applyImageDeleted(ImageDeleted $imageDeleted) {

        $internalId = $imageDeleted->getInternalId();
        if ($imageDeleted->getInternalId()) {
          $file = File::load($internalId);
          // Delete the usage, cron will  delete the file if this was the only usage.
          if ($file) {
            $this->fileUsage->delete($file, 'culturefeed_udb3', 'udb3_item', $imageUpdated->getEventId());
          }
        }
    }

}
