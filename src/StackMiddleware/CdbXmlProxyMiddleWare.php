<?php

namespace Drupal\culturefeed_udb3\StackMiddleware;

use CultuurNet\UDB3\Symfony\Proxy\CdbXmlProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class CdbXmlProxyMiddleWare
 *
 * @package Drupal\culturefeed_udb3\StackMiddleware
 */
class CdbXmlProxyMiddleWare implements HttpKernelInterface{

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

  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = TRUE) {

    $response = $this->proxy->handle($request);

    if (!empty($response)) {
      return $response;
    }
    else {
      return $this->httpKernel->handle($request, $type, $catch);
    }

  }

}
