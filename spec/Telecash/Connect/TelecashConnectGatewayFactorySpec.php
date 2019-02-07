<?php

namespace spec\Turbine\SyliusTelecashPlugin\Telecash\Connect;

use Payum\Core\GatewayFactory;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\TelecashConnectGatewayFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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
