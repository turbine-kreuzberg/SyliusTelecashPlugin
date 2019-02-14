<?php

declare(strict_types=1);

namespace Tests\Turbine\SyliusTelecashPlugin\Behat\Page\External;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface TelecashConnectPageInterface extends PageInterface
{
    /**
     * @throws UnsupportedDriverActionException
     * @throws DriverException
     */
    public function pay();

    /**
     * @throws UnsupportedDriverActionException
     * @throws DriverException
     */
    public function cancel();
}