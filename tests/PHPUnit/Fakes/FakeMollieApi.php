<?php

namespace Kiener\MolliePayments\Tests\Fakes;

use Kiener\MolliePayments\Components\Mollie\Api\MollieApiInterface;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;

class FakeMollieApi implements MollieApiInterface
{

    /**
     * @var array<mixed>
     */
    private $usedParams;


    /**
     * @return mixed[]
     */
    public function getUsedParams(): array
    {
        return $this->usedParams;
    }

    /**
     * @param array $params
     * @return Payment
     */
    public function createPayment(array $params): Payment
    {
        $this->usedParams = $params;

        return new Payment(new MollieApiClient());
    }

}
