<?php

declare(strict_types=1);

namespace Turbine\SyliusTelecashPlugin\Telecash\Connect\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Api;

/**
 * Class CaptureOffsiteAction
 *
 * @property \Turbine\SyliusTelecashPlugin\Telecash\Connect\Api $api
 */
class CaptureOffsiteAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * {@inheritdoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $httpRequest = new GetHttpRequest();
        $this->gateway->execute($httpRequest);

        //we are back from telecash site so we have to just update model.
        if ($httpRequest->method === 'POST') {
            //check response hash signature
            if (!$this->api->isResponseHashValid($details['telecash_request'], $httpRequest->request)) {
                throw new InvalidArgumentException('Not valid response');
            }

            $details['telecash_response'] = $httpRequest->request;

            return;
        }

        $details[Api::PAYMENT_RESPONSE_SUCCESS_URL] = $request->getToken()->getTargetUrl();
        $details[Api::PAYMENT_RESPONSE_FAIL_URL] = $request->getToken()->getTargetUrl();

        $details['telecash_request'] =
            $this->api->prepareOffsitePayment($details->toUnsafeArray());

        throw new HttpPostRedirect(
            $this->api->getOffsiteUrl(),
            $details['telecash_request']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return $request instanceof Capture && $request->getModel() instanceof \ArrayAccess;
    }
}
