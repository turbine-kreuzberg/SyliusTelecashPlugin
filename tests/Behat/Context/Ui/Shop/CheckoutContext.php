<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;

final class CheckoutContext implements Context
{

    /**
     * @When /^I cancel my Telecash Connect payment$/
     * @Given /^I have cancelled Telecash Connect payment$/
     */
    public function iCancelMyTelecashConnectPayment()
    {
        throw new PendingException();
    }

    /**
     * @Given /^I sign in to Telecash Connect and pay successfully$/
     */
    public function iSignInToTelecashConnectAndPaySuccessfully()
    {
        throw new PendingException();
    }

    /**
     * @When /^I try to pay again Telecash Connect payment$/
     */
    public function iTryToPayAgainTelecashConnectPayment()
    {
        throw new PendingException();
    }
}