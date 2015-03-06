<?php
/**
 * @file
 */

namespace Drupal\culturefeed_udb3\Controller;

use CultuurNet\UDB3\Event\ReadModel\DocumentRepositoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventHistoryController extends ControllerBase {

  /**
   * @var DocumentRepositoryInterface
   */
  protected $documentRepository;

  public function __construct(DocumentRepositoryInterface $repository) {
    $this->documentRepository = $repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('culturefeed_udb3.event_history_repository')
    );
  }

  /**
   * Returns an event's history.
   *
   * @param string $cdbid
   *   The event id.
   *
   * @return JsonResponse
   *   The event history as JSON.
   */
  public function history($cdbid) {
    /** @var \CultuurNet\UDB3\Event\ReadModel\JsonDocument $document */
    $document = $this->documentRepository->get($cdbid);

    $response = JsonResponse::create()
      ->setContent($document->getRawBody())
      ->setPublic()
      ->setClientTtl(60 * 5)
      ->setTtl(60 * 1);

    $response->headers->set('Vary', 'Origin');

    return $response;
  }
}
