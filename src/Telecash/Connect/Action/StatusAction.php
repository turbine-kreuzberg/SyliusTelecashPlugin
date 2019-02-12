<?php

namespace Turbine\SyliusTelecashPlugin\Telecash\Connect\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Api;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = new ArrayObject($request->getModel());

        if (null === $model['telecash_request']) {
            $request->markNew();

            return;
        }

        if (isset($model['telecash_response']) && Api::isResponseSuccess($model['telecash_response'])) {
            $request->markCaptured();

            return;
        }

        if (isset($model['telecash_response']) && Api::isResponseWait($model['telecash_response'])) {
            $request->markPending();

            return;
        }

        $request->markFailed();
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }
}
