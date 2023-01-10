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
     * @var MollieApi
     */
    private $client;

    /**
     * @var NumberFormatter
     */
    private $numberFormatter;


    /**
     * @param string $apiKey
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
     * @return string
     * @throws \Mollie\Api\Exceptions\ApiException
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
