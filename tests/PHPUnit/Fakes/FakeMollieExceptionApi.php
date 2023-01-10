<?php

namespace Kiener\MolliePayments\Tests\Fakes;

use Kiener\MolliePayments\Components\Mollie\Api\MollieApiInterface;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Payment;

class FakeMollieExceptionApi implements MollieApiInterface
{

    /**
     * @var string
     */
    private $message;

    /**
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param array<mixed> $params
     * @return Payment
     * @throws ApiException
     */
    public function createPayment(array $params): Payment
    {
        throw new ApiException($this->message);
    }

}