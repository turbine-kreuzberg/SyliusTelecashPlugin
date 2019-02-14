<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Page\External;

use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\Page;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\Payment;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\BrowserKit\Client;
use Turbine\SyliusTelecashPlugin\Telecash\Connect\Api;

final class TelecashConnectPage extends Page implements TelecashConnectPageInterface
{
    /** @var RepositoryInterface */
    private $securityTokenRepository;

    /** @var RepositoryInterface */
    private $paymentRepository;

    public function __construct(
        Session $session,
        $minkParameters,
        RepositoryInterface $securityTokenRepository,
        RepositoryInterface $paymentRepository
    ) {
        parent::__construct($session, $minkParameters);

        $this->securityTokenRepository = $securityTokenRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUrl(array $urlParameters = []): string
    {
        return 'https://www.telecash.de';
    }

    /**
     * {@inheritdoc}
     */
    public function pay()
    {
        $token = $this->findCaptureToken();

        $this->getClient()->request(
            'POST',
            $token->getTargetUrl(),
            $this->getSuccessResultData($token)
        );
        //$this->getDriver()->visit($token->getAfterUrl());
    }

    /**
     * {@inheritdoc}
     */
    public function cancel()
    {
        $token = $this->findCaptureToken();

        $this->getClient()->request(
            'POST',
            $token->getTargetUrl(),
            $this->getFailedResultData($token)
        );
        //$this->getDriver()->visit($token->getAfterUrl());
    }

    /**
     * @return TokenInterface
     *
     * @throws \RuntimeException
     */
    private function findCaptureToken()
    {
        /** @var TokenInterface[] $tokens */
        $tokens = $this->securityTokenRepository->findAll();

        foreach ($tokens as $token) {
            if (strpos($token->getTargetUrl(), 'capture')) {
                return $token;
            }
        }

        throw new \RuntimeException('Cannot find capture token, check if you are after proper checkout steps');
    }

    /**
     * @return Client
     *
     * @throws UnsupportedDriverActionException
     */
    private function getClient()
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof BrowserKitDriver) {
            throw new UnsupportedDriverActionException(
                'You need to tag the scenario with ' .
                '"@mink:symfony2". ' .
                'Intercepting the redirections is not ' .
                'supported by %s', $this->getSession()->getDriver()
            );
        }

        return $driver->getClient();
    }

    private function getSuccessResultData(TokenInterface $token): array
    {
        /** @var Payment $payment */
        $payment = $this->paymentRepository->find($token->getDetails()->getId());
        $request = $payment->getDetails()['telecash_request'];
        $resultData = [
            Api::PAYMENT_APPROVAL_CODE => 'Y:valid',
            Api::PAYMENT_CHARGE_TOTAL => $request[Api::PAYMENT_CHARGE_TOTAL],
            Api::PAYMENT_CURRENCY => $request[Api::PAYMENT_CURRENCY],
            Api::PAYMENT_TXN_DATETIME => $request[Api::PAYMENT_TXN_DATETIME],
            Api::PAYMENT_STORE_NAME => $request[Api::PAYMENT_STORE_NAME],
        ];

        $resultData[Api::PAYMENT_RESPONSE_HASH] =
            Api::generateHash(array_merge(['TEST_KEY'], array_values($resultData)));

        return $resultData;
    }

    private function getFailedResultData(TokenInterface $token, int $resultCode = 1): array
    {
        /** @var Payment $payment */
        $payment = $this->paymentRepository->find($token->getDetails()->getId());
        $request = $payment->getDetails()['telecash_request'];
        $resultData = [
            Api::PAYMENT_APPROVAL_CODE => 'N:invalid',
            Api::PAYMENT_CHARGE_TOTAL => $request[Api::PAYMENT_CHARGE_TOTAL],
            Api::PAYMENT_CURRENCY => $request[Api::PAYMENT_CURRENCY],
            Api::PAYMENT_TXN_DATETIME => $request[Api::PAYMENT_TXN_DATETIME],
            Api::PAYMENT_STORE_NAME => $request[Api::PAYMENT_STORE_NAME],
        ];

        $resultData[Api::PAYMENT_RESPONSE_HASH] =
            Api::generateHash(array_merge(['TEST_KEY'], array_values($resultData)));

        return $resultData;
    }
}
