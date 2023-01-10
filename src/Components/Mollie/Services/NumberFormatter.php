<?php

namespace Kiener\MolliePayments\Components\Mollie\Services;

class NumberFormatter
{

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
