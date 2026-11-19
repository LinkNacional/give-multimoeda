<?php

namespace Lkn\GiveMultimoedas\Includes;

// Exit, if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Normalizes exchange rate payloads and builds the map GiveWP expects.
 *
 * @since 3.1.7
 */
final class GiveMultiCurrencyRates
{
    /**
     * Normalize an API payload into a currency => rate map relative to the base currency.
     *
     * Accepts either a full payload ({"rates": {...}}) or the bare rates map.
     *
     * @since 3.1.7
     *
     * @param array  $payload      Decoded API response.
     * @param string $baseCurrency Currency used to request the rates.
     *
     * @return array<string, float>
     */
    public static function normalize_rates(array $payload, string $baseCurrency): array
    {
        $baseCurrency = strtoupper($baseCurrency);

        $rates = isset($payload['rates']) && is_array($payload['rates']) ? $payload['rates'] : $payload;

        $normalized = array();

        foreach ($rates as $code => $rate) {
            if (! is_string($code) && ! is_int($code)) {
                continue;
            }

            if (! is_numeric($rate) || (float) $rate <= 0) {
                continue;
            }

            $normalized[strtoupper((string) $code)] = (float) $rate;
        }

        if (! isset($normalized[$baseCurrency])) {
            $normalized[$baseCurrency] = 1.0;
        }

        return $normalized;
    }

    /**
     * Build the exchange rates GiveWP expects for the currency switcher.
     *
     * GiveWP treats an exchange rate of 0 as "this is the base currency", so the
     * base currency must be 0 while every other currency carries its multiplier.
     *
     * @since 3.1.7
     *
     * @param array  $rates        Decoded API payload or bare rates map.
     * @param string $baseCurrency Currency used to request the rates.
     * @param array  $currencies   Currencies that will be exposed in the form.
     *
     * @return array<string, float>
     */
    public static function build_switcher_rates(array $rates, string $baseCurrency, array $currencies): array
    {
        $baseCurrency = strtoupper($baseCurrency);
        $normalized = self::normalize_rates($rates, $baseCurrency);

        $result = array();

        foreach ($currencies as $currency) {
            $currency = strtoupper((string) $currency);

            if ('' === $currency) {
                continue;
            }

            if ($currency === $baseCurrency) {
                $result[$currency] = 0.0;

                continue;
            }

            $result[$currency] = $normalized[$currency] ?? 1.0;
        }

        return $result;
    }

    /**
     * Convert an amount between two currencies using the switcher rate map.
     *
     * @since 3.1.7
     *
     * @param float  $amount Amount in the source currency.
     * @param string $from   Source currency code.
     * @param string $to     Target currency code.
     * @param array  $rates  Map produced by build_switcher_rates().
     *
     * @return float
     */
    public static function convert(float $amount, string $from, string $to, array $rates): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        if ($from === $to || $amount <= 0) {
            return $amount;
        }

        $fromRate = self::rate_for($from, $rates);
        $toRate = self::rate_for($to, $rates);

        if ($fromRate <= 0 || $toRate <= 0) {
            return $amount;
        }

        return $amount / $fromRate * $toRate;
    }

    /**
     * Resolve a currency multiplier, treating the base currency as 1.
     *
     * @since 3.1.7
     */
    private static function rate_for(string $currency, array $rates): float
    {
        if (! isset($rates[$currency]) || 0.0 === (float) $rates[$currency]) {
            return 1.0;
        }

        return (float) $rates[$currency];
    }
}
