<?php

namespace App;

use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

/**
 * Convert input money to Money object.
 */
function as_money($amount, string $currency): Money
{
    $parser = new DecimalMoneyParser(new ISOCurrencies());

    return $parser->parse((string) $amount, new Currency($currency));
}

/**
 * Present the money to user.
 */
function present_money(Money $money): string
{
    $formatter = new DecimalMoneyFormatter(new ISOCurrencies());

    return \sprintf('%s %s', (string) $money->getCurrency(), $formatter->format($money));
}
