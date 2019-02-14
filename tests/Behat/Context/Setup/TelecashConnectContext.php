<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class TelecashConnectContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    /** @var ExampleFactoryInterface */
    private $paymentMethodExampleFactory;

    /** @var ObjectManager */
    private $paymentMethodManager;

    /** @var array */
    private $gatewayFactories;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExampleFactoryInterface $paymentMethodExampleFactory,
        ObjectManager $paymentMethodManager,
        array $gatewayFactories
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodExampleFactory = $paymentMethodExampleFactory;
        $this->paymentMethodManager = $paymentMethodManager;
        $this->gatewayFactories = $gatewayFactories;
    }

    /**
     * @Given the store has (also) a payment method :paymentMethodName with a code :paymentMethodCode and Telecash Connect payment gateway
     */
    public function theStoreHasAPaymentMethodWithACodeAndTelecashConnectPaymentGateway(
        string $paymentMethodName,
        string $paymentMethodCode
    ): void {
        $paymentMethod = $this->createPaymentMethod(
            $paymentMethodName,
            $paymentMethodCode,
            'Telecash Connect'
        );
        $paymentMethod->getGatewayConfig()->setConfig([
            'store_id' => 'TEST',
            'user_id' => 'TEST',
            'shared_secret' => 'TEST_KEY',
            'sandbox' => true,
        ]);

        $this->paymentMethodManager->flush();
    }

    /**
     * @param string $name
     * @param string $code
     * @param string $gatewayFactory
     * @param string $description
     * @param bool $addForCurrentChannel
     * @param int|null $position
     *
     * @return PaymentMethodInterface
     */
    private function createPaymentMethod(
        $name,
        $code,
        $gatewayFactory = 'Offline',
        $description = '',
        $addForCurrentChannel = true,
        $position = null
    ) {
        $gatewayFactory = array_search($gatewayFactory, $this->gatewayFactories);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodExampleFactory->create([
            'name' => ucfirst($name),
            'code' => $code,
            'description' => $description,
            'gatewayName' => $gatewayFactory,
            'gatewayFactory' => $gatewayFactory,
            'enabled' => true,
            'channels' => ($addForCurrentChannel && $this->sharedStorage->has('channel')) ? [$this->sharedStorage->get('channel')] : [],
        ]);

        if (null !== $position) {
            $paymentMethod->setPosition((int) $position);
        }

        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);

        return $paymentMethod;
    }
}