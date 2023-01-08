<?php

namespace Kiener\MolliePayments\Components\Mollie;


use Kiener\MolliePayments\Setting\PluginConfiguration;
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
     * @return string
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function createPayment(string $method, float $amount): string
    {
        $params = [
            'amount' => [
                'value' => $amount,
                'currency' => 'EUR',
            ],
            'method' => $method,
            'redirectUrl' => '',
            'webhookUrl' => '',
            'description' => '',
        ];

        $payment = $this->client->payments->create($params);

        return (string)$payment->getCheckoutUrl();
    }

}
