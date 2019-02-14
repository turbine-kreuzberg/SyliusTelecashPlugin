<?php

declare(strict_types=1);

namespace Turbine\SyliusTelecashPlugin\Telecash\Connect;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;

class Api
{
    public const PAYMENT_TXN_TYPE = 'txntype';
    public const PAYMENT_TIMEZONE = 'timezone';
    public const PAYMENT_TXN_DATETIME = 'txndatetime';
    public const PAYMENT_HASH_ALGORITHM = 'hash_algorithm';
    public const PAYMENT_HASH = 'hash';
    public const PAYMENT_STORE_NAME = 'storename';
    public const PAYMENT_MODE = 'mode';
    public const PAYMENT_PAYMENT_METHOD = 'paymentMethod';
    public const PAYMENT_CHECKOUT_OPTION = 'checkoutoption';
    public const PAYMENT_CHARGE_TOTAL = 'chargetotal';
    public const PAYMENT_CURRENCY = 'currency';
    public const PAYMENT_LANGUAGE = 'language';
    public const PAYMENT_RESPONSE_SUCCESS_URL = 'responseSuccessURL';
    public const PAYMENT_RESPONSE_FAIL_URL = 'responseFailURL';
    public const PAYMENT_APPROVAL_CODE = 'approval_code';
    public const PAYMENT_RESPONSE_HASH = 'response_hash';

    public const PAYMENT_TXN_TYPE_SALE = 'sale';

    public const PAYMENT_CHECKOUT_OPTION_CLASSIC = 'classic';

    public const PAYMENT_MODE_PAYONLY = 'payonly';

    public const PAYMENT_HASH_ALGORITHM_SHA256 = 'SHA256';
    public const PAYMENT_HASH_ALGORITHM_SHA512 = 'SHA512';

    /**
     * @var array|ArrayObject
     */
    protected $options = [
        'sandbox' => null,
        'store_id' => null,
        'user_id' => null,
        'shared_secret' => null,
        'checkout_option' => self::PAYMENT_CHECKOUT_OPTION_CLASSIC,
        'mode' => self::PAYMENT_MODE_PAYONLY,
        'hash_algorithm' => self::PAYMENT_HASH_ALGORITHM_SHA256,
    ];

    /**
     * @var array
     */
    protected $fieldsToHash = [
        self::PAYMENT_STORE_NAME,
        self::PAYMENT_TXN_DATETIME,
        self::PAYMENT_CHARGE_TOTAL,
        self::PAYMENT_CURRENCY,
    ];

    /**
     * Api constructor.
     *
     * @param mixed $options
     */
    public function __construct(array $options)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);
        $options->validateNotEmpty([
            'store_id',
            'user_id',
            'shared_secret',
        ]);

        if (!is_bool($options['sandbox'])) {
            throw new LogicException('The boolean sandbox option must be set.');
        }

        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getOffsiteUrl(): string
    {
        return $this->options['sandbox'] ?
            'https://test.ipg-online.com/connect/gateway/processing' :
            'https://TODO_get_the_real_url_for_live'
            ;
    }

    /**
     * @param array $model
     *
     * @return array
     */
    public function prepareOffsitePayment(array $model): array
    {
        $fields = [
            self::PAYMENT_TXN_TYPE => self::PAYMENT_TXN_TYPE_SALE,
            self::PAYMENT_TIMEZONE => date_default_timezone_get(),
            self::PAYMENT_TXN_DATETIME => date('Y:m:d-H:i:s'),
            self::PAYMENT_HASH_ALGORITHM => $this->options['hash_algorithm'],
            self::PAYMENT_STORE_NAME => $this->options['store_id'],
            self::PAYMENT_MODE => $this->options['mode'],
            self::PAYMENT_CHECKOUT_OPTION => $this->options['checkout_option'],
            self::PAYMENT_CHARGE_TOTAL => $model[self::PAYMENT_CHARGE_TOTAL],
            self::PAYMENT_CURRENCY => $model[self::PAYMENT_CURRENCY],
            self::PAYMENT_LANGUAGE => 'en_US', //FIXME: set current channel language. See Connect Integration Guide page10-11
            self::PAYMENT_RESPONSE_SUCCESS_URL => $model[self::PAYMENT_RESPONSE_SUCCESS_URL],
            self::PAYMENT_RESPONSE_FAIL_URL => $model[self::PAYMENT_RESPONSE_FAIL_URL],
        ];

        $fields[self::PAYMENT_HASH] = self::generateHash(
            [
                $fields[self::PAYMENT_STORE_NAME],
                $fields[self::PAYMENT_TXN_DATETIME],
                $fields[self::PAYMENT_CHARGE_TOTAL],
                $fields[self::PAYMENT_CURRENCY],
                $this->options['shared_secret'],
            ],
            $this->options['hash_algorithm']
        );

        return $fields;
    }

    /**
     * @param array $request
     * @param array $response
     *
     * @return bool
     */
    public function isResponseHashValid(array $request, array $response): bool
    {
        $calculatedHash = self::generateHash(
            [
                $this->options['shared_secret'],
                $response[self::PAYMENT_APPROVAL_CODE],
                $request[self::PAYMENT_CHARGE_TOTAL],
                $request[self::PAYMENT_CURRENCY],
                $request[self::PAYMENT_TXN_DATETIME],
                $request[self::PAYMENT_STORE_NAME],
            ],
            $this->options['hash_algorithm']
        );

        return $calculatedHash === $response[self::PAYMENT_RESPONSE_HASH];
    }

    /**
     * @param array $response
     *
     * @return bool
     */
    public static function isResponseSuccess(array $response): bool
    {
        return strpos($response[self::PAYMENT_APPROVAL_CODE] ?? '', 'Y') === 0;
    }

    /**
     * @param array $response
     *
     * @return bool
     */
    public static function isResponseWait(array $response): bool
    {
        return strpos($response[self::PAYMENT_APPROVAL_CODE] ?? '', '?') === 0;
    }

    /**
     * @param $fields
     * @param string $hash_algorithm
     *
     * @return string
     */
    public static function generateHash(
        $fields,
        $hash_algorithm = self::PAYMENT_HASH_ALGORITHM_SHA256
    ): string {
        $stringToHash = '';
        foreach ($fields as $value) {
            $stringToHash .= $value;
        }

        return hash(
            strtolower($hash_algorithm),
            bin2hex($stringToHash)
        );
    }
}
