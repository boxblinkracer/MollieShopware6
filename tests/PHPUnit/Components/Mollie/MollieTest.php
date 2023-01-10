<?php

namespace Kiener\MolliePayments\Tests\Components\Mollie;

use Kiener\MolliePayments\Components\Mollie\Mollie;
use Kiener\MolliePayments\Tests\Fakes\FakeMollieApi;
use Kiener\MolliePayments\Tests\Fakes\FakeMollieExceptionApi;
use Mollie\Api\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;

class MollieTest extends TestCase
{


    /**
     * @return void
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function testCorrectPayload()
    {
        $fakeClient = new FakeMollieApi();

        $mollie = new Mollie($fakeClient);

        $mollie->createPayment('p24', 15.9, 'test@mollie.com');

        $expected = [
            'amount' => [
                'value' => '15.90',
                'currency' => 'EUR'
            ],
            'method' => 'p24',
            'redirectUrl' => 'https://mollie.com',
            'description' => 'Shopware Order',
            'billingEmail' => 'test@mollie.com',
        ];

        $this->assertEquals($expected, $fakeClient->getUsedParams());
    }

    /**
     * @return void
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function testExceptionHandling()
    {
        $this->expectException(ApiException::class);

        $fakeClient = new FakeMollieExceptionApi('phpunit error');

        $mollie = new Mollie($fakeClient);

        $mollie->createPayment('p24', 15.9, 'test@mollie.com');
    }
}