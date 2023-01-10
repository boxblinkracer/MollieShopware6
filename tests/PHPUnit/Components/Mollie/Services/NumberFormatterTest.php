<?php

namespace Kiener\MolliePayments\Tests\Components\MollieServices;

use Kiener\MolliePayments\Components\Mollie\Services\NumberFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatterTest extends TestCase
{

    /**
     * @var NumberFormatter
     */
    private $formatter;


    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->formatter = new NumberFormatter();
    }

    /**
     * @return void
     */
    public function testFormatValue()
    {
        $result = $this->formatter->formatValue(14);

        $this->assertEquals('14.00', $result);
    }

}
