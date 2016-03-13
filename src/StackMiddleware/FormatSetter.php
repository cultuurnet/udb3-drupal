<?php

namespace Drupal\culturefeed_udb3\StackMiddleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class FormatSetter.
 *
 * @package Drupal\culturefeed_udb3\StackMiddleware
 */
class FormatSetter implements HttpKernelInterface {

  /**
   * The wrapped HTTP kernel.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * Constructs a PageCache object.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The decorated kernel.
   */
  public function __construct(HttpKernelInterface $http_kernel) {
    $this->httpKernel = $http_kernel;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {
    // Add json ld as a valid format.
    $request->setFormat('ld_json', 'application/ld+json');

    // Ensure Accept headers are used.
    if ($request->headers->has('Accept')) {
      $content_types = $request->getAcceptableContentTypes();
      foreach ($content_types as $content_type) {
        $format = $request->getFormat($content_type);
        if ($format) {
          $request->setRequestFormat($request->getFormat($content_type));
          break;
        }
      }
    }
    return $this->httpKernel->handle($request, $type, $catch);
  }

}
