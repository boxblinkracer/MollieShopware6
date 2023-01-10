<?php

namespace Kiener\MolliePayments\Components\Mollie;

use Kiener\MolliePayments\Components\Configuration\PluginConfiguration;
use Kiener\MolliePayments\Components\Mollie\Api\MollieApi;
use Kiener\MolliePayments\Components\Mollie\Api\MollieApiInterface;
use Kiener\MolliePayments\Components\Mollie\Services\NumberFormatter;
use Mollie\Api\MollieApiClient;

class Mollie
{

    /**
     * @var MollieApiInterface
     */
    private $client;

    /**
     * @var NumberFormatter
     */
    private $numberFormatter;


    /**
     * @param MollieApiInterface $client
     */
    public function __construct(MollieApiInterface $client)
    {
        $this->client = $client;

        $this->numberFormatter = new NumberFormatter();
    }

    /**
     * @param string $method
     * @param float $amount
     * @param string $billingEmail
     * @throws \Mollie\Api\Exceptions\ApiException
     * @return string
     */
    public function createPayment(string $method, float $amount, string $billingEmail): string
    {
        $params = [
            'amount' => [
                'value' => $this->numberFormatter->formatValue($amount),
                'currency' => 'EUR',
            ],
            'method' => $method,
            'redirectUrl' => 'https://mollie.com',
            'description' => 'Shopware Order',
            'billingEmail' => $billingEmail,
        ];

        $payment = $this->client->createPayment($params);

        return (string)$payment->getCheckoutUrl();
    }
}
