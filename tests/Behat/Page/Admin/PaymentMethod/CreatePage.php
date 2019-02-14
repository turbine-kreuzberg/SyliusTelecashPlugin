<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function setTelecashConnectGatewaySandbox(bool $value): void
    {
        $this->getDocument()->checkField('Sandbox');
    }

    public function setTelecashConnectGatewayStoreId(string $value): void
    {
        $this->getDocument()->fillField('Store Id', $value);
    }

    public function setTelecashConnectGatewayUserId(string $value): void
    {
        $this->getDocument()->fillField('User Id', $value);
    }

    public function setTelecashConnectGatewaySharedSecret(string $value): void
    {
        $this->getDocument()->fillField('Shared Secret', $value);
    }

    public function setTelecashConnectGatewayHashAlgorithm(string $value): void
    {
        $this->getDocument()->selectFieldOption('Hash Algorithm', $value);
    }
}
