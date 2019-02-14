<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\Turbine\SyliusTelecashPlugin\Behat\Page\Admin\PaymentMethod\CreatePage;
use Webmozart\Assert\Assert;

final class ManagingPaymentMethodsContext implements Context
{
    /**
     * @var CreatePage
     */
    private $createPage;

    /**
     * @param CreatePage $createPage
     */
    public function __construct(CreatePage $createPage)
    {
        $this->createPage = $createPage;
    }

    /**
     * @Given /^I want to create a new payment method with Telecash Connect gateway factory$/
     */
    public function iWantToCreateANewPaymentMethodWithTelecashConnectGatewayFactory()
    {
        $this->createPage->open(['factory' => 'telecash_connect']);
    }

    /**
     * @Given /^I configure it with test Telecash credentials$/
     */
    public function iConfigureItWithTestTelecashCredentials()
    {
        $this->createPage->setTelecashConnectGatewaySandbox(true);
        $this->createPage->setTelecashConnectGatewayStoreId('test_store_id');
        $this->createPage->setTelecashConnectGatewayUserId('test_user_id');
        $this->createPage->setTelecashConnectGatewaySharedSecret('test_shared_secret');
    }

    /**
     * @Then I should be notified that the secure key is invalid
     */
    public function iShouldBeNotifiedThatTheStoreIdIsInvalid()
    {
        Assert::same(
            $this->createPage->getValidationMessage('store_id'),
            'Store Id can\'t be empty.'
        );
    }

    /**
     * @Then I should be notified that the merchant ID is invalid
     */
    public function iShouldBeNotifiedThatTheUserIdIsInvalid()
    {
        Assert::same(
            $this->createPage->getValidationMessage('user_id'),
            'User Id can\'t be empty.'
        );
    }

    /**
     * @Then I should be notified that the Key version is invalid
     */
    public function iShouldBeNotifiedThatTheSharedSecretIsInvalid()
    {
        Assert::same(
            $this->createPage->getValidationMessage('shared_secret'),
            'Shared Secret can\'t be empty.'
        );
    }
}
