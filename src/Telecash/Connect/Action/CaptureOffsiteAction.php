<?php


namespace Turbine\SyliusTelecashPlugin\Telecash\Connect\Action;


use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\RenderTemplate;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Api;

class CaptureOffsiteAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $httpRequest = new GetHttpRequest();
        $this->gateway->execute($httpRequest);

        //we are back from telecash site so we have to just update model.
        if (isset($httpRequest->query['EXECCODE'])) { //TODO: extract relevant data from telecash callback
            $model['telecash_response'] = $this->checkAndUpdateResponse($httpRequest);//TODO: pass relevant data
        } else {
            //TODO: prepare model

            throw new HttpPostRedirect(
                $this->api->getOffsiteUrl(),
                $this->api->prepareOffsitePayment($model->toUnsafeArray())
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Capture && $request->getModel() instanceof \ArrayAccess;
    }

    protected function checkAndUpdateResponse($response)
    {
        if (!$this->api->isResponseValid($response)) {
            throw new RequestNotSupportedException('Not valid response');
        }
        //TODO: update response if needed
        return $response;
    }
}