<?php

/**
 * @file
 * Contains Drupal\culturefeed_udb3\Store\EventStore.
 */

namespace Drupal\culturefeed_udb3\Store;

use Broadway\Domain\DateTime;
use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainEventStreamInterface;
use Broadway\Domain\DomainMessage;
use Broadway\EventStore\EventStoreInterface;
use Broadway\EventStore\EventStreamNotFoundException;
use Broadway\Serializer\SerializerInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;

/**
 * Class EventStore.
 *
 * @package Drupal\culturefeed_udb3
 */
class EventStore implements EventStoreInterface {

  /**
   * The entity Manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface;
   */
  protected $entityManager;

  /**
   * The query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory;
   */
  protected $entityQuery;

  /**
   * The payload serializer.
   *
   * @var \Broadway\Serializer\SerializerInterface;
   */
  protected $payloadSerializer;

  /**
   * The metadata serializer.
   *
   * @var \Broadway\Serializer\SerializerInterface
   */
  protected $metadataSerializer;

  /**
   * Constructs the event store.
   *
   * @param string $entity_type
   *   The Domain Message Entity Type.
   * @param EntityManagerInterface $entity_manager
   *   The entity manger.
   * @param QueryFactory $entity_query
   *   The query factory.
   * @param SerializerInterface $payload_serializer
   *   The payload serializer.
   * @param SerializerInterface $metadata_serializer
   *   The metadata serializer.
   */
  public function __construct(
    $entity_type,
    EntityManagerInterface $entity_manager,
    QueryFactory $entity_query,
    SerializerInterface $payload_serializer,
    SerializerInterface $metadata_serializer
  ) {
    $this->entityManager = $entity_manager->getStorage($entity_type);
    $this->entityQuery = $entity_query->get($entity_type);
    $this->payloadSerializer  = $payload_serializer;
    $this->metadataSerializer = $metadata_serializer;
  }

  /**
   * {@inheritdoc}
   */
  public function load($id) {

    $query = $this->entityQuery->condition('uuid', $id);
    $result = $query->execute();

    $events = array();
    foreach ($result as $uuid) {
      $events[] = $this->deserializeEvent($uuid);
    }

    if (empty($events)) {
      throw new EventStreamNotFoundException(sprintf('EventStream not found for aggregate with id %s', $id));
    }

    return new DomainEventStream($events);

  }

  /**
   * {@inheritdoc}
   */
  public function append($id, DomainEventStreamInterface $event_stream) {

    try {
      foreach ($event_stream as $domain_message) {
        $this->insertMessage($domain_message);
      }
    }
    catch (\Exception $exception) {
      throw $exception;
    }

  }

  /**
   * Insert domain message.
   *
   * @param DomainMessage $domain_message
   *   The domain message.
   */
  private function insertMessage(DomainMessage $domain_message) {

    $message = $this->entityManager->create(array(
      'uuid' => $domain_message->getId(),
      'playhead' => $domain_message->getPlayhead(),
      'metadata' => json_encode($this->metadataSerializer->serialize($domain_message->getMetadata())),
      'payload' => json_encode($this->payloadSerializer->serialize($domain_message->getPayload())),
      'recorded_on' => $domain_message->getRecordedOn()->toString(),
      'type' => $domain_message->getType(),
    ));
    $message->save();

  }

  /**
   * Deserialize the entity.
   *
   * @param int $entity_id
   *   The entity id.
   *
   * @return DomainMessage
   *   The domain message.
   */
  private function deserializeEvent($entity_id) {

    $domain_message = $this->entityManager->load($entity_id);
    return new DomainMessage(
      $domain_message->uuid->value,
      $domain_message->playhead->value,
      $this->metadataSerializer->deserialize(json_decode($domain_message->metadata->value, TRUE)),
      $this->payloadSerializer->deserialize(json_decode($domain_message->payload->value, TRUE)),
      DateTime::fromString($domain_message->recorded_on->value)
    );

  }

}
