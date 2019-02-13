<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Test\Services\DefaultChannelFactory;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class ChannelContext implements Context
{
    /**
     * @var DefaultChannelFactory
     */
    private $defaultChannelFactory;

    /**
     * @param DefaultChannelFactory $defaultChannelFactory
     */
    public function __construct(DefaultChannelFactory $defaultChannelFactory)
    {
        $this->defaultChannelFactory = $defaultChannelFactory;
    }

    /**
     * @Given adding a new channel :name with code :code and currency :currencyCode
     */
    public function addingANewChannelWithCodeAndCurrency(
        string $name,
        string $code,
        string $currencyCode
    ): void {
        $this->defaultChannelFactory->create($code, $name, $currencyCode);
    }
}