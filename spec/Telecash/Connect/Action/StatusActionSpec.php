<?php

declare(strict_types=1);

namespace spec\Turbine\SyliusTelecashPlugin\Telecash\Connect\Action;

use Payum\Core\Action\ActionInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Request\GetStatus;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Action\StatusAction;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Api;

class StatusActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(StatusAction::class);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ActionInterface::class);
    }

    function it_supports_only_get_status_request_and_array_access(
        GetStatus $request,
        \ArrayAccess $arrayAccess
    ): void {
        $request->getModel()->willReturn($arrayAccess);
        $this->supports($request)->shouldReturn(true);
    }

    function it_executes_and_mark_as_new(GetStatus $request, \ArrayObject $arrayObject)
    {
        $request->getModel()->willReturn($arrayObject);
        $request->markNew()->shouldBeCalled();

        $this->execute($request);
    }

    function it_executes_and_mark_as_captured(GetStatus $request)
    {
        $model = new \ArrayObject([
            'telecash_request' => [],
            'telecash_response' => [
                Api::PAYMENT_APPROVAL_CODE => 'Y:success',
            ],
        ]);

        $request->getModel()->willReturn($model);
        $request->markCaptured()->shouldBeCalled();

        $this->execute($request);
    }

    function it_executes_and_mark_as_pending(GetStatus $request)
    {
        $model = new \ArrayObject([
            'telecash_request' => [],
            'telecash_response' => [
                Api::PAYMENT_APPROVAL_CODE => '?:pending',
            ],
        ]);
        $request->getModel()->willReturn($model);
        $request->markPending()->shouldBeCalled();

        $this->execute($request);
    }

    function it_executes_and_mark_as_failed(GetStatus $request)
    {
        $model = new \ArrayObject([
            'telecash_request' => [],
            'telecash_response' => [
                Api::PAYMENT_APPROVAL_CODE => 'N:failed',
            ],
        ]);
        $request->getModel()->willReturn($model);
        $request->markFailed()->shouldBeCalled();

        $this->execute($request);
    }
}
