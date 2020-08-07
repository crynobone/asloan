<?php

namespace App;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

/**
 * Present the money to user.
 */
function present_money(Money $money): string
{
    $formatter = new DecimalMoneyFormatter(new ISOCurrencies());

    return \sprintf('%s %s', (string) $money->getCurrency(), $formatter->format($money));
}
