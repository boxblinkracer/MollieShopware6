<?php

namespace Kiener\MolliePayments\Components\Mollie\Api;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

class MollieApi implements MollieApiInterface
{

    /**
     * @var string
     */
    private $apiKey;


    /**
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param array<mixed> $params
     * @throws \Mollie\Api\Exceptions\ApiException
     * @return Payment
     */
    public function createPayment(array $params): Payment
    {
        $client = new MollieApiClient();
        $client->setApiKey($this->apiKey);

        return $client->payments->create($params);
    }
}
