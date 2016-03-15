<?php

namespace Drupal\culturefeed_udb3\Factory;

use Broadway\CommandHandling\CommandBusInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use CultuurNet\UDB3\Media\ImageUploaderService;
use Drupal\Core\Config\ConfigFactoryInterface;
use League\Flysystem\FilesystemInterface;
use ValueObjects\Number\Natural;

/**
 * Class ImageUploaderFactory.
 *
 * @package Drupal\culturefeed_udb3\Factory
 */
class ImageUploaderFactory {

  /**
   * The command bus.
   *
   * @var \Broadway\CommandHandling\CommandBusInterface
   */
  protected $commandBus;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * The file system.
   *
   * @var \League\Flysystem\FilesystemInterface
   */
  protected $fileSystem;

  /**
   * The media upload directory.
   *
   * @var string
   */
  protected $mediaUploadDirectory;

  /**
   * The uuid generator.
   *
   * @var \Broadway\UuidGenerator\UuidGeneratorInterface
   */
  protected $uuidGenerator;

  /**
   * ImageUploaderFactory constructor.
   *
   * @param \Broadway\UuidGenerator\UuidGeneratorInterface $uuid_generator
   *   The uuid generator.
   * @param \Broadway\CommandHandling\CommandBusInterface $command_bus
   *   The command bus.
   * @param \League\Flysystem\FilesystemInterface $file_system
   *   The file system.
   * @param string $media_upload_directory
   *   The media upload directory.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory.
   */
  public function __construct(
    UuidGeneratorInterface $uuid_generator,
    CommandBusInterface $command_bus,
    FilesystemInterface $file_system,
    $media_upload_directory,
    ConfigFactoryInterface $config
  ) {
    $this->uuidGenerator = $uuid_generator;
    $this->commandBus = $command_bus;
    $this->fileSystem = $file_system;
    $this->mediaUploadDirectory = $media_upload_directory;
    $this->config = $config->get('culturefeed_udb3.settings');
  }

  /**
   * Return an image uploader service.
   *
   * @return \CultuurNet\UDB3\Media\ImageUploaderService
   *   The image uploader service.
   */
  public function get() {

    $file_size_limit = new Natural($this->config->get('media.file_size_limit'));
    return new ImageUploaderService(
      $this->uuidGenerator,
      $this->commandBus,
      $this->fileSystem,
      $this->mediaUploadDirectory,
      $file_size_limit
    );

  }

}
