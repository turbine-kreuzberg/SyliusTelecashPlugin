<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;


interface CreatePageInterface extends BaseCreatePageInterface
{
    public function setTelecashConnectGatewaySandbox(bool $value): void;

    public function setTelecashConnectGatewayStoreId(string $value): void;

    public function setTelecashConnectGatewayUserId(string $value): void;

    public function setTelecashConnectGatewaySharedSecret(string $value): void;

    public function setTelecashConnectGatewayHashAlgorithm(string $value): void;
}