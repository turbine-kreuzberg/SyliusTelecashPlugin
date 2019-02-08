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
        if (!$config['payum.api']) {
            $config['payum.default_options'] = [
                'sandbox' => true,
                'store_id' => null,
                'user_id' => null,
                'shared_secret' => null,
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

                return new Api(
                    [
                        'sandbox' => $config['sandbox'],
                        'store_id' => $config['store_id'],
                        'user_id' => $config['user_id'],
                        'shared_secret' => $config['shared_secret'],
                        'mode' => $config['mode'],
                        'hash_algorithm' => $config['hash_algorithm'],
                    ]
                );
            };
        }
    }
}
