<?php

namespace Lkn\GiveMultimoedas\Tests;

use Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyRates;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyRates
 */
final class GiveMultiCurrencyRatesTest extends TestCase
{
    public function test_normalize_rates_reads_the_rates_envelope(): void
    {
        $payload = array('rates' => array('BRL' => 1, 'USD' => 0.1721, 'EUR' => 0.1568));

        $rates = GiveMultiCurrencyRates::normalize_rates($payload, 'BRL');

        self::assertSame(1.0, $rates['BRL']);
        self::assertSame(0.1721, $rates['USD']);
        self::assertSame(0.1568, $rates['EUR']);
    }

    public function test_normalize_rates_accepts_a_bare_map_and_uppercases_keys(): void
    {
        $rates = GiveMultiCurrencyRates::normalize_rates(array('brl' => 1, 'usd' => 0.17), 'BRL');

        self::assertArrayHasKey('BRL', $rates);
        self::assertArrayHasKey('USD', $rates);
    }

    public function test_normalize_rates_discards_invalid_values_and_backfills_the_base(): void
    {
        $rates = GiveMultiCurrencyRates::normalize_rates(
            array('rates' => array('USD' => 0, 'EUR' => -1, 'JPY' => 'nope', 'GBP' => 0.13)),
            'BRL'
        );

        self::assertSame(array('GBP' => 0.13, 'BRL' => 1.0), $rates);
    }

    public function test_build_switcher_rates_marks_the_base_currency_with_zero(): void
    {
        $rates = GiveMultiCurrencyRates::build_switcher_rates(
            array('rates' => array('BRL' => 1, 'USD' => 0.1721, 'EUR' => 0.1568)),
            'BRL',
            array('BRL', 'USD', 'EUR')
        );

        // GiveWP treats rate 0 as "base currency"; anything else must be the multiplier.
        self::assertSame(0.0, $rates['BRL']);
        self::assertSame(0.1721, $rates['USD']);
        self::assertSame(0.1568, $rates['EUR']);
    }

    public function test_build_switcher_rates_falls_back_to_one_for_unknown_currencies(): void
    {
        $rates = GiveMultiCurrencyRates::build_switcher_rates(
            array('rates' => array('BRL' => 1, 'USD' => 0.17)),
            'BRL',
            array('BRL', 'USD', 'CHF')
        );

        self::assertSame(1.0, $rates['CHF']);
    }

    public function test_convert_moves_between_non_base_currencies(): void
    {
        $rates = array('BRL' => 0.0, 'USD' => 0.1721, 'EUR' => 0.1568);

        // 100 USD -> BRL -> EUR
        $eur = GiveMultiCurrencyRates::convert(100.0, 'USD', 'EUR', $rates);

        self::assertEqualsWithDelta(91.11, $eur, 0.05);
    }

    public function test_convert_returns_the_same_amount_for_the_same_currency(): void
    {
        $rates = array('BRL' => 0.0, 'USD' => 0.1721);

        self::assertSame(50.0, GiveMultiCurrencyRates::convert(50.0, 'USD', 'USD', $rates));
    }

    public function test_convert_treats_the_base_currency_as_one(): void
    {
        $rates = array('BRL' => 0.0, 'USD' => 0.1721);

        // 100 BRL -> USD
        self::assertEqualsWithDelta(17.21, GiveMultiCurrencyRates::convert(100.0, 'BRL', 'USD', $rates), 0.001);
        // 100 USD -> BRL
        self::assertEqualsWithDelta(581.06, GiveMultiCurrencyRates::convert(100.0, 'USD', 'BRL', $rates), 0.05);
    }
}
