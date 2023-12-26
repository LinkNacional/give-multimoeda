<?php

// Exit, if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Query the actual exchange rates in the Link Nacional servers
 *
 * @param array $currenciesCode
 *
 * @return string $suportedCurrencies
 *
 */
function lkn_give_multi_currency_get_exchange_rates($currenciesCode) {
    $exRate = [];

    foreach ($currenciesCode as $key => $currency) {
        $result = lkn_multi_currency_curl_get_contents('https://api.linknacional.com/cotacao/cotacao-' . $currency . '.json');
        $result = json_decode($result);
        $exRate[$currency] = $result->rates->BRL;
    }

    // retorna um array com o rate das moedas ativas
    return json_encode($exRate);
}

/**
 * Gets all active currencies symbols
 *
 * @param array $currenciesCode
 *
 * @return string $currenciesSymbol
 *
 */
function lkn_give_multi_currency_get_symbols($currenciesCode) {
    $currenciesSymbol['BRL'] = 'R$';

    if (!empty($currenciesCode)) {
        for ($c = 0; $c < count($currenciesCode); $c++) {
            $currenciesSymbol[$currenciesCode[$c]] = give_currency_symbol($currenciesCode[$c], true);
        }
    }

    return json_encode($currenciesSymbol);
}

/**
 * Query a new request
 *
 * @param string $url
 *
 * @return string $response
 *
 */
function lkn_multi_currency_curl_get_contents($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
