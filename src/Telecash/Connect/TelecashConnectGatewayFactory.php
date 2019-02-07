<?php

declare(strict_types=1);

namespace Turbine\SyliusTelecashPlugin\Telecash\Connect;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class TelecashConnectGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritdoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'telecash_connect',
            'payum.factory_title' => 'Telecash Connect',
        ]);
        if (false === $config['payum.api']) {
            $config['payum.default_options'] = [
                'sandbox' => 1,
                'store_id' => '',
                'user_id' => '',
                'shared_secret' => '',
                'mode' => 'payonly',
                'hash_algorithm' => 'SHA256',
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'store_id',
                'user_id',
                'shared_secret',
            ];
            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return new Api();
            };
        }
    }
}
