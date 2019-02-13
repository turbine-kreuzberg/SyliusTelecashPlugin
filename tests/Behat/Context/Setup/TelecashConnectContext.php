<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;

final class TelecashConnectContext implements Context
{

    /**
     * @Given /^the store has a payment method :paymentMethodName with a code :code and Telecash Connect payment gateway$/
     */
    public function theStoreHasAPaymentMethodWithACodeAndTelecashConnectPaymentGateway(
        string $paymentMethodName,
        string $code
    ): void {
        throw new PendingException();
    }
}