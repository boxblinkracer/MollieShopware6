<?php

namespace Kiener\MolliePayments\Components\Mollie;


use Kiener\MolliePayments\Components\Configuration\PluginConfiguration;
use Mollie\Api\MollieApiClient;

class Mollie
{

    /**
     * @var MollieApiClient
     */
    private $client;


    /**
     * @param PluginConfiguration $configuration
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function __construct(PluginConfiguration $configuration)
    {
        $this->client = new MollieApiClient();
        $this->client->setApiKey($configuration->getApiKey());
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
                'value' => $this->formatValue($amount),
                'currency' => 'EUR',
            ],
            'method' => $method,
            'redirectUrl' => 'https://mollie.com',
            'description' => 'Shopware Order',
            'billingEmail' => $billingEmail,
        ];


        $payment = $this->client->payments->create($params);

        return (string)$payment->getCheckoutUrl();
    }

    /**
     * @param null|float $price
     * @return string
     */
    public function formatValue(?float $price)
    {
        if (is_null($price)) {
            $price = 0.0;
        }

        return number_format(round($price, 2), 2, '.', '');
    }

}
