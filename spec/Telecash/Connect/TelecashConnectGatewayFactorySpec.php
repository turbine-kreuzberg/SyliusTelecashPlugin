<?php

declare(strict_types=1);

namespace spec\Turbine\SyliusTelecashPlugin\Telecash\Connect;

use Payum\Core\GatewayFactory;
use PhpSpec\ObjectBehavior;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\TelecashConnectGatewayFactory;

class TelecashConnectGatewayFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TelecashConnectGatewayFactory::class);
        $this->shouldHaveType(GatewayFactory::class);
    }

    function it_populateConfig_run()
    {
        $this->createConfig([])->shouldBeArray();
    }
}
