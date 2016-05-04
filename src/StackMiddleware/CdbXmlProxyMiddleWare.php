<?php

namespace Drupal\culturefeed_udb3\StackMiddleware;

use CultuurNet\UDB3\Symfony\Proxy\CdbXmlProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class CdbXmlProxyMiddleWare.
 *
 * @package Drupal\culturefeed_udb3\StackMiddleware
 */
class CdbXmlProxyMiddleWare implements HttpKernelInterface {

  /**
   * The http kernel.
   *
   * @var \Symfony\Component\HttpKernel\HttpKernelInterface
   */
  protected $httpKernel;

  /**
   * The cdbxml proxy.
   *
   * @var \CultuurNet\UDB3\Symfony\Proxy\CdbXmlProxy
   */
  protected $proxy;

  /**
   * CdbXmlProxyMiddleWare constructor.
   *
   * @param \Symfony\Component\HttpKernel\HttpKernelInterface $http_kernel
   *   The http kernel.
   * @param \CultuurNet\UDB3\Symfony\Proxy\CdbXmlProxy $proxy
   *   The cdbxml proxy.
   */
  public function __construct(HttpKernelInterface $http_kernel, CdbXmlProxy $proxy) {
    $this->httpKernel = $http_kernel;
    $this->proxy = $proxy;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {

    // Create a request clone so the drupal udb3 path prefix can be stripped.
    $path_info = $request->getPathInfo();
    $path = str_replace('udb3/api/1.0/', '', $path_info);
    $cdbxml_request = $request->duplicate(
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      array_merge($request->server->all(), array('REQUEST_URI' => $path))
    );

    $cdbxml_response = $this->proxy->handle($cdbxml_request);

    if (!empty($cdbxml_response)) {
      return $cdbxml_response;
    }
    else {
      return $this->httpKernel->handle($request, $type, $catch);
    }

  }

}
