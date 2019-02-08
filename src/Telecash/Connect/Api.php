<?php

declare(strict_types=1);

namespace Turbine\SyliusTelecashPlugin\Telecash\Connect;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;

class Api
{
    public $mode;

    /**
     * @var array|ArrayObject
     */
    protected $options = [
        'sandbox' => null,
        'store_id' => null,
        'user_id' => null,
        'shared_secret' => null,
        'mode' => null,
        'hash_algorithm' => null,
    ];

    /**
     * Api constructor.
     * @param mixed $options
     */
    public function __construct(array $options)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);
        $options->validateNotEmpty(array(
            'store_id',
            'user_id',
            'shared_secret',
        ));

        if (!is_bool($options['sandbox'])) {
            throw new LogicException('The boolean sandbox option must be set.');
        }

        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getOffsiteUrl(): string {
        return $this->options['sandbox'] ?
            'https://test.ipg-online.com/connect/gateway/processing' :
            'https://TODO_get_the_real_url_for_live'
            ;
    }

    /**
     * @param array $model
     * @return array
     */
    public function prepareOffsitePayment(array $model): array {
        return [
            'txntype' => 'sale',
            'timezone' => 'Europe/Berlin', //FIX
            'txndatetime' => 'FIX',
            'hash_algorithm' => $this->options['hash_algorithm'],
            'hash' => 'FIX:get the hash',
            'storename' => $this->options['store_id'],
            'mode' => $this->options['mode'],
            'checkoutoption' => 'classic', //FIX: is this right
            'chargetotal' => '13.00', //FIX: get the real value
            'currency' => '978', //FIX: where is this comming?
            'language' => 'en_US', //FIX
            'responseSuccessURL' => 'an url', //FIX
            'responseFailURL' => 'an url', //FIX
        ];
    }
}
