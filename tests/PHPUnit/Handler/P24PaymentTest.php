<?php

namespace Kiener\MolliePayments\Tests\Handler;


use Kiener\MolliePayments\Handler\P24Payment;
use PHPUnit\Framework\TestCase;

class P24PaymentTest extends TestCase
{

    /**
     * @return void
     */
    public function testIdentifier()
    {
        $this->assertEquals('Kiener\MolliePayments\Handler\P24Payment', P24Payment::class);
    }

}
