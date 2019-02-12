<?php

namespace Turbine\SyliusTelecashPlugin\Telecash\Connect\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use Payum\Core\Request\GetCurrency;
use Payum\Core\Storage\IdentityInterface;
use Payum\ISO4217\ISO4217;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Api;

class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Convert $request
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $this->gateway->execute($currency = new GetCurrency($payment->getCurrencyCode()));

        $details = ArrayObject::ensureArrayObject($payment->getDetails());
        $details[Api::PAYMENT_CURRENCY] = $currency->numeric;
        $details[Api::PAYMENT_CHARGE_TOTAL] =
            number_format(
                $payment->getTotalAmount() / (10 ** $currency->exp),
                $currency->exp,
                '.',
                ''
            );

        $request->setResult((array) $details);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Convert &&
            $request->getSource() instanceof PaymentInterface &&
            $request->getTo() === 'array'
        ;
    }
}
