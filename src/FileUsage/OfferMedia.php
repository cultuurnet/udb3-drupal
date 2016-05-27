<?php

namespace Drupal\culturefeed_udb3\FileUsage;

use Broadway\EventHandling\EventListenerInterface;
use CultuurNet\UDB3\EventHandling\DelegateEventHandlingToSpecificMethodTrait;
use CultuurNet\UDB3\Offer\Events\Image\AbstractImageAdded;
use CultuurNet\UDB3\Offer\Events\Image\AbstractImageRemoved;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\FileUsage\FileUsageInterface;

/**
 * Class EventMedia.
 *
 * @package Drupal\culturefeed_udb3\FileUsage
 */
class OfferMedia implements EventListenerInterface {

  use DelegateEventHandlingToSpecificMethodTrait;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * The file usage backend.
   *
   * @var \Drupal\file\FileUsage\FileUsageInterface
   */
  protected $fileUsage;

  /**
   * The media directory.
   *
   * @var string
   */
  protected $mediaDirectory;

  /**
   * The stream uri.
   *
   * @var string
   */
  protected $streamUri;

  /**
   * The udb3offer type.
   *
   * @var string
   */
  protected $type;

  /**
   * Construct the listener.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param string $media_directory
   *   The media directory.
   * @param string $stream_uri
   *   The stream uri.
   * @param \Drupal\file\FileUsage\FileUsageInterface $file_usage
   *   The file usage backend.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param string $type
   *   The udb3 offer type.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    $media_directory,
    $stream_uri,
    FileUsageInterface $file_usage,
    EntityRepositoryInterface $entity_repository,
    $type
  ) {
    $this->entityRepository = $entity_repository;
    $this->mediaDirectory = $media_directory;
    $this->streamUri = $stream_uri;
    $this->fileUsage = $file_usage;
    $this->entityStorage = $entity_type_manager->getStorage('file');
    $this->type = $type;
  }

  /**
   * Add file to Drupal file system when added.
   *
   * @param \CultuurNet\UDB3\Offer\Events\Image\AbstractImageAdded $image_added
   *   The image added event.
   */
  protected function offerImageAdded(AbstractImageAdded $image_added) {

    try {

      $image = $image_added->getImage();
      // Translate the source location back to a Drupal stream wrapper uri.
      $uri = $this->streamUri . $this->mediaDirectory . '/' . basename($image->getSourceLocation());
      $id = $image->getMediaObjectId();

      // Check if the image doesn't exist yet (in case of a replay).
      if (empty($this->entityRepository->loadEntityByUuid('file', $id))) {

        /* @var \Drupal\file\Entity\File $file */
        $file = $this->entityStorage->create(array(
          'uuid' => $id,
          'uri' => $uri,
          'status' => FILE_STATUS_PERMANENT,
        ));
        $file->save();
        $this->fileUsage->add($file, 'culturefeed_udb3', $this->type, $image_added->getItemId());

      }

    }
    catch (\Exception $e) {
      // Silently fail.
    }

  }

  /**
   * Cleanup file entities after image delete.
   *
   * @param \CultuurNet\UDB3\Offer\Events\Image\AbstractImageRemoved $image_removed
   *   The image removed event.
   */
  protected function offerImageRemoved(AbstractImageRemoved $image_removed) {

    try {

      $image = $image_removed->getImage();
      $file = $this->entityRepository->loadEntityByUuid('file', $image->getMediaObjectId());
      $file->delete();

    }
    catch (\Exception $e) {
      // Silently fail.
    }

  }

}
