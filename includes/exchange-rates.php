<?php

// Exit, if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Pega a cotação das moedas selecionadas de acordo com a api da linknacional
 *
 * @param array $currenciesCode
 *
 * @return string $suportedCurrencies
 *
 */
function give_multi_currency_get_exchange_rates($currenciesCode) {
    $cotacao = lkn_multimoeda_curl_get_contents('https://api.linknacional.com.br/cotacao/cotacao-BRL.json');

    $cotacao = json_decode($cotacao, true);
    $cotacao = $cotacao['rates'];

    // pega só os exchange rate das moedas ativas
    for ($c = 0; $c < count($currenciesCode); $c++) {
        foreach ($cotacao as $code => $value) {
            if ($currenciesCode[$c] == $code) {
                $suportedCurrencies[$code] = "$value";
            }
        }
    }

    // retorna um array com o rate das moedas ativas
    return json_encode($suportedCurrencies);
}

/**
 * Pega todos os símbolos das moedas ativas
 *
 * @param array $currenciesCode
 *
 * @return string $currenciesSymbol
 *
 */
function give_multi_currency_get_symbols($currenciesCode) {
    $currenciesSymbol['BRL'] = 'R$';

    for ($c = 0; $c < count($currenciesCode); $c++) {
        $currenciesSymbol[$currenciesCode[$c]] = give_currency_symbol($currenciesCode[$c], true);
    }

    return json_encode($currenciesSymbol);
}

/**
 * Faz a requisição do tipo GET
 *
 * @param string $url
 *
 * @return string $response
 *
 */
function lkn_multimoeda_curl_get_contents($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}
