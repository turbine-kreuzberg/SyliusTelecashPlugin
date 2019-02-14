<?php

declare(strict_types=1);

namespace Turbine\SyliusTelecashPlugin\Telecash\Connect\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Api;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

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

        //FIX: canceled by user is also marked as failed, can we use response code to figure out
        //if user cancelled?
        $request->markFailed();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }
}
