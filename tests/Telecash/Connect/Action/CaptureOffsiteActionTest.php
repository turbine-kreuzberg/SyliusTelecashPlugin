<?php

namespace Tests\Turbine\SyliusTelecashPlugin\Telecash\Connect\Action;

use function Clue\StreamFilter\fun;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Generic;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Tests\GenericActionTest;
use PHPUnit\Framework\MockObject\MockObject;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Action\CaptureOffsiteAction;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Api;

/**
 * Class CaptureOffsiteActionTest
 * @property CaptureOffsiteAction $action
 *
 * @package Tests\Turbine\SyliusTelecashPlugin\Telecash\Connect\Action
 */
class CaptureOffsiteActionTest extends GenericActionTest
{
    protected $actionClass = CaptureOffsiteAction::class;

    protected $requestClass = Capture::class;

    /**
     * @var Api|MockObject
     */
    protected $api;

    /**
     * @var GatewayAwareInterface|MockObject
     */
    protected $gateway;

    protected function setUp()
    {
        $this->action = new $this->actionClass();
        $this->api = $this->createApiMock();
        $this->gateway = $this->createGatewayMock();

        $this->action->setApi($this->api);
        $this->action->setGateway($this->gateway);
    }


    public function provideSupportedRequests()
    {
        return [
            [new $this->requestClass([])]
        ];
    }

    public function provideNotSupportedRequests()
    {
        return [
            ['foo'],
            [['foo']],
            [new \stdClass()],
            [$this->getMockForAbstractClass(Generic::class, [[]])],
            [new $this->requestClass(new \stdClass(), 'array')],
        ];
    }

    /**
     * Override to pass test as tests without assertions are marked as risky
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
            $this->assertNotNull(new $this->actionClass());
    }

    /**
     * @test
     */
    public function shouldRedirectToOffsite()
    {
        $token = $this->createMock(TokenInterface::class);
        $token->expects(self::any())
            ->method('getTargetUrl')
            ->willReturn('http://a-target-url');
        $capture = new Capture($token);
        $capture->setModel([]);

        $this->api->expects(self::once())
            ->method('getOffsiteUrl')
            ->willReturn('http://the-offsite-url');

        try{
            $this->action->execute($capture);
        } catch (HttpPostRedirect $e) {
            $this->assertEquals('http://the-offsite-url', $e->getUrl());
        }

    }

    /**
     * @test
     */
    public function shouldSetResponseIfValid()
    {
        $this->gateway->expects(self::once())
            ->method('execute')
            ->willReturnCallback(function(GetHttpRequest $request) {
               $request->request['approval_code'] = 'Y:valid';
            });
        $capture = new Capture([
            'telecash_request' => []
        ]);

        $this->api->expects(self::once())
            ->method('isResponseHashValid')
            ->willReturn(true);

        $this->action->execute($capture);
        $this->assertEquals(
            ['approval_code' => 'Y:valid'],
            $capture->getModel()['telecash_response']
        );
    }

    /**
     * @test
     * @expectedException \Payum\Core\Exception\InvalidArgumentException
     */
    public function shouldThrowIfResponseInvalid()
    {
        $this->gateway->expects(self::once())
            ->method('execute')
            ->willReturnCallback(function(GetHttpRequest $request) {
                $request->request['approval_code'] = 'Y:valid';
            });
        $capture = new Capture([
            'telecash_request' => []
        ]);

        $this->api->expects(self::once())
            ->method('isResponseHashValid')
            ->willReturn(false);

        $this->action->execute($capture);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Api
     */
    protected function createApiMock()
    {
        return $this->createMock(Api::class);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GatewayInterface
     */
    protected function createGatewayMock()
    {
        return $this->createMock(GatewayInterface::class);
    }
}
