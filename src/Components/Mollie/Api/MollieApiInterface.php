<?php

namespace Kiener\MolliePayments\Components\Mollie\Api;

use Mollie\Api\Resources\Payment;

interface MollieApiInterface
{

    /**
     * @param array<mixed> $params
     * @return Payment
     */
    public function createPayment(array $params): Payment;
}
