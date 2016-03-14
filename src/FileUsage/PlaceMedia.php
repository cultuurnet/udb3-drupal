<?php

namespace Drupal\culturefeed_udb3\FileUsage;

use Broadway\EventHandling\EventListenerInterface;
use CultuurNet\UDB3\EventHandling\DelegateEventHandlingToSpecificMethodTrait;
use CultuurNet\UDB3\Place\Events\ImageRemoved;
use CultuurNet\UDB3\Place\Events\ImageAdded;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\file\FileUsage\FileUsageInterface;

/**
 * Class PlaceMedia.
 *
 * @package Drupal\culturefeed_udb3\FileUsage
 */
class PlaceMedia implements EventListenerInterface {

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
   * Construct the listener.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\file\FileUsage\FileUsageInterface $file_usage
   *   The file usage backend.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    FileUsageInterface $file_usage,
    EntityRepositoryInterface $entity_repository
  ) {
    $this->entityRepository = $entity_repository;
    $this->fileUsage = $file_usage;
    $this->entityStorage = $entity_type_manager->getStorage('file');
  }

  /**
   * Add file to Drupal file system when added.
   *
   * @param \CultuurNet\UDB3\Place\Events\ImageAdded $image_added
   *   The image added event.
   */
  protected function applyImageAdded(ImageAdded $image_added) {

    try {

      $image = $image_added->getImage();

      /* @var \Drupal\file\Entity\File $file */
      $file = $this->entityStorage->create(array(
        'uuid' => $image->getMediaObjectId(),
        'uri' => $image->getSourceLocation(),
        'status' => FILE_STATUS_PERMANENT,
      ));
      $file->save();
      $this->fileUsage->add($file, 'culturefeed_udb3', 'place', $image_added->getItemId());

    }
    catch (\Exception $e) {
      // Silently die.
    }

  }

  /**
   * Cleanup file entities after image delete.
   *
   * @param \CultuurNet\UDB3\Place\Events\ImageRemoved $image_removed
   *   The image removed event.
   */
  protected function applyImageRemoved(ImageRemoved $image_removed) {

    try {

      $image = $image_removed->getImage();
      $file = $this->entityRepository->loadEntityByUuid('file', $image->getMediaObjectId());
      $file->delete();

    }
    catch (\Exception $e) {
      // Silently die.
    }

  }

}
