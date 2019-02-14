<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Tests\Turbine\SyliusTelecashPlugin\Behat\Page\External\TelecashConnectPageInterface;

final class TelecashConnectContext implements Context
{
    /** @var TelecashConnectPageInterface */
    private $telecashConnectPage;

    /** @var ShowPageInterface */
    private $orderDetails;

    public function __construct(
        TelecashConnectPageInterface $telecashConnectPage,
        ShowPageInterface $orderDetails
    ) {
        $this->telecashConnectPage = $telecashConnectPage;
        $this->orderDetails = $orderDetails;
    }

    /**
     * @When /^I cancel my Telecash Connect payment$/
     * @Given /^I have cancelled Telecash Connect payment$/
     */
    public function iCancelMyTelecashConnectPayment()
    {
        $this->telecashConnectPage->cancel();
    }

    /**
     * @Given /^I sign in to Telecash Connect and pay successfully$/
     */
    public function iSignInToTelecashConnectAndPaySuccessfully()
    {
        $this->telecashConnectPage->pay();
    }

    /**
     * @When /^I try to pay again Telecash Connect payment$/
     */
    public function iTryToPayAgainTelecashConnectPayment()
    {
        $this->orderDetails->pay();
    }

    /**
     * @Then I should be notified that my payment has failed
     */
    public function iShouldBeNotifiedThatMyPaymentHasBeenCancelled()
    {
        $this->assertNotification('Payment has failed.');
    }

    /**
     * @param string $expectedNotification
     */
    private function assertNotification($expectedNotification)
    {
        $notifications = $this->orderDetails->getNotifications();
        $hasNotifications = '';

        foreach ($notifications as $notification) {
            $hasNotifications .= $notification;
            if ($notification === $expectedNotification) {
                return;
            }
        }

        throw new \RuntimeException(sprintf('There is no notification with "%s". Got "%s"', $expectedNotification, $hasNotifications));
    }
}