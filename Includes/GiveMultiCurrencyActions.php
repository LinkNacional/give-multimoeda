<?php

namespace Lkn\GiveMultimoedas\Includes;
// Exit, if accessed directly.
if ( ! defined('ABSPATH')) {
    exit;
}

final class GiveMultiCurrencyActions {
    /**
     * This function centralizes the data in one spot for ease mannagment
     *
     * @return array
     */
    public static function lkn_give_multi_currency_get_configs() {
        $configs = array();

        $configs['mcEnabled'] = self::lkn_give_multi_currency_get_enabled();
        $configs['mainCurrency'] = self::lkn_give_multi_currency_get_default_currency();
        $configs['activeCurrency'] = self::lkn_give_multi_currency_get_active_currency();
        $configs['defaultCoin'] = self::lkn_give_multi_currency_get_default_coin();

        return $configs;
    }

    /**
     * Checks if the 'multi currency' is enabled
     *
     * @return string enabled | disabled
     *
     */
    public static function lkn_give_multi_currency_get_enabled() {
        $enabled = give_get_option('multi_currency_enabled_setting_field');

        return $enabled;
    }

    /**
     * Gets the default currency from give
     *
     * @return string enabled | disabled
     *
     */
    public static function lkn_give_multi_currency_get_default_currency() {
        $mainCurrency = give_get_option('currency');

        return $mainCurrency;
    }

    /**
     * Gets default currency defined on admin settings
     *
     * @return string
     */
    public static function lkn_give_multi_currency_get_default_coin() {
        $defaultCoin = give_get_option('multi_currency_default_currency');

        return $defaultCoin;
    }

    /**
     * Checks the active currency for the plugin
     *
     * @return string|array
     */
    public static function lkn_give_multi_currency_get_active_currency() {
        $currency = give_get_option('multi_currency_active_currency');

        if ( ! empty($currency)) {
            // Conversion to uppercase
            for ($c = 0; $c < count($currency); $c++) {
                $currency[$c] = strtoupper($currency[$c]);
            }
        } else {
            return false;
        }

        return $currency;
    }

    public static function lkn_give_change_multi_currency($currency) {
        // checks if a foreign currency was selected and the gateway is not paypal donations
        if ( ! empty($_POST['give-mc-selected-currency']) && 'paypal-commerce' !== $_POST['payment-mode']) {
            $currency = $_POST['give-mc-selected-currency'];
        }

        return $currency;
    }

    public static function lkn_give_multi_currency_thousand_separator($separator) {
        $separator = '.';

        return $separator;
    }

    /**
     * Fix the decimal separator
     *
     * @param string $separator The decimal separator
     *
     * @return string
     *
     */
    public static function lkn_give_multi_currency_decimal_separator($separator) {
        $separator = ',';

        return $separator;
    }

    /**
     * Fix the decimal count
     *
     * @param string $separator The decimal count
     *
     * @return string
     *
     */
    public static function lkn_give_multi_currency_decimal_count($count) {
        $count = 0;

        return $count;
    }

    /** ===== Multi Currency front-end actions ===== */

    /**
     * Builds the Multi Currency Front-end
     *
     * @param int $form_id
     *
     * @param array $args
     *
     * @return bool|void
     *
     */
    public static function lkn_give_multi_currency_selector($form_id, $args) {
        $id_prefix = ! empty($args['id_prefix']) ? $args['id_prefix'] : '';
        $configs = self::lkn_give_multi_currency_get_configs();
        $pluginEnabled = $configs['mcEnabled'];
        $mainCurrency = $configs['mainCurrency'];
        $mainCurrencyName = give_get_currency_name($mainCurrency);
        $activeCurrencyNames = array();
        $activeCurrency = $configs['activeCurrency'];
        $hasValidGateway = 'false';
        $activeSymbolArr = GiveMultiCurrencyHelper::lkn_give_multi_currency_get_symbols($activeCurrency);
        $defaultCoin = $configs['defaultCoin'];
        $html = null;

        $globalConfigs = get_post_meta($form_id, 'lkn_multi_currency_fields_status', true);
        if ('disabled' === $globalConfigs) {
            $defaultCoin = get_post_meta($form_id, 'lkn_multi_currency_fields_default_currency', true);
            $activeCurrency = get_post_meta($form_id, 'lkn_multi_currency_fields_active_currency', true);
            $activeSymbolArr = GiveMultiCurrencyHelper::lkn_give_multi_currency_get_symbols($activeCurrency);
        }

        // Get all payment gateways
        $gateways = give_get_payment_gateways();
        // Search for plugin keys
        $gateways = array_keys($gateways);
        for ($c = 0; $c < count($gateways); $c++) {
            // Found a license key
            $optionName = give_get_option($gateways[$c] . '_setting_field');
            if ($optionName) {
                $hasValidGateway = 'true';

                break;
            }
        }

        if ('enabled' !== $pluginEnabled || false == $activeCurrency) {
            // If no active currency don't render the selector
            return false;
        }
        if ('BRL' !== $mainCurrency) {
            Give()->notices->print_frontend_notice(
                sprintf(
                    '<strong>%1$s</strong> %2$s',
                    esc_html__('Erro:', 'give'),
                    esc_html__('Plugin Multi Moedas só funciona com a moeda base reais (R$).', 'give')
                )
            );
        } elseif (give_get_option('number_decimals') > 0) {
            Give()->notices->print_frontend_notice(
                sprintf(
                    '<strong>%1$s</strong> %2$s',
                    esc_html__('Erro:', 'give'),
                    esc_html__('Remova as casas decimais do valor da doação', 'give')
                )
            );
        } elseif ( ! in_array($configs['defaultCoin'], $activeCurrency, true) && 'BRL' !== $configs['defaultCoin']) {
            Give()->notices->print_frontend_notice(
                sprintf(
                    '<strong>%1$s</strong> %2$s',
                    esc_html__('Erro:', 'give'),
                    esc_html__('Moeda padrão não está ativa no Multi Moedas', 'give')
                )
            );
        } else {
            // Saves all active currencies from Give WP
            for ($c = 0; $c < count($activeCurrency); $c++) {
                $activeCurrencyNames[] = give_get_currency_name($activeCurrency[$c]);
            }

            // Compatibility with Paypal-Commerce gateway
            if (give_is_gateway_active('paypal-commerce')) {
                $exchangeRate = GiveMultiCurrencyHelper::lkn_give_multi_currency_get_exchange_rates($activeCurrency);
            } else {
                $exchangeRate = wp_json_encode('disabled');
            }

            // Front-end with EOT

            // To pass the attributes to javascript correctly it is necessary to convert to JSON
            $activeCurrency = wp_json_encode($activeCurrency);
            $activeCurrencyNames = wp_json_encode($activeCurrencyNames);

            $html = '<script>

// Global attributes that do not depend on HTML elements
var currencySymbolArray = ' . $activeSymbolArr . ';
var exRate = ' . $exchangeRate . ';
var hasValidGateway = "' . $hasValidGateway . '";
var summaryIntervalId = null;
var legacyIntervalId = null;
var classicBtnIntervalId = null;

/**
 * Change the label of the final total value of the legacy form
 * 
 * @return void
 */
function changeLabelFinalAmountLegacy() {
    let updateTotalAmountFn = function () {
        let giveFinalPrice = document.getElementsByClassName(\'give-final-total-amount\')[0];
        let currencyCode = document.getElementById(\'give-mc-select\');

        if (giveFinalPrice) {
            let pastSymbol = giveFinalPrice.textContent;

            // Cycles through the entire string to find and replace the currency symbol
            for (let c = 0; c < pastSymbol.length; c++) {
                if (/\d/.test(pastSymbol.charAt(c)) === true) {
                    pastSymbol = pastSymbol.substring(0, c);
                    break;
                }

                if (c > 100) {
                    console.error(\'Inifinite loop exception\');
                    break;
                }
            }

            // Change the text inside the html if it is a different currency symbol when timeout runs
            if (pastSymbol !== giveFinalPrice.textContent) {
                giveFinalPrice.textContent = giveFinalPrice.textContent.replace(pastSymbol, currencySymbolArray[currencyCode.value]);
            }
        }
    };

    if (legacyIntervalId) {
        clearInterval(legacyIntervalId);
        legacyIntervalId = setInterval(updateTotalAmountFn, 1000);
    } else {
        legacyIntervalId = setInterval(updateTotalAmountFn, 1000);
    }
}

/**
 * Change label for Legacy template
 * 
 * @return void
 */
function changeLabelLegacy() {

    let priceList = document.getElementById(\'give-donation-level-button-wrap\');
    let currencyCode = document.getElementById(\'give-mc-select\');

    let giveFinalPrice = document.getElementsByClassName(\'give-final-total-amount\')[0];
    let pastSymbol = giveFinalPrice.textContent;

    // Cycles through the entire string to find and replace the currency symbol
    for (let c = 0; c < pastSymbol.length; c++) {
        if (/\d/.test(pastSymbol.charAt(c)) === true) {
            pastSymbol = pastSymbol.substring(0, c);
            break;
        }

        if (c > 100) {
            console.error(\'Inifinite loop exception\');
            break;
        }

    }

    // Changes currency inside HTML element
    if (pastSymbol !== giveFinalPrice.textContent) {
        giveFinalPrice.textContent = giveFinalPrice.textContent.replace(pastSymbol, currencySymbolArray[currencyCode.value]);
    }

    if (priceList) {
        let elemPriceList = priceList.getElementsByTagName(\'li\');
        for (let c = 0; c < elemPriceList.length; c++) {

            let nodeChild = elemPriceList[c].children;
            pastSymbol = nodeChild[0].textContent;

            for (let i = 0; i < pastSymbol.length; i++) {
                if (/\d/.test(pastSymbol.charAt(i)) === true) {
                    pastSymbol = pastSymbol.substring(0, i);
                    break;
                }

                if (i > 100) {
                    console.error(\'Inifinite loop exception\');
                    break;
                }

            }
            if (pastSymbol !== nodeChild[0].textContent) {
                nodeChild[0].textContent = nodeChild[0].textContent.replace(pastSymbol, currencySymbolArray[currencyCode.value]);
            }

        }

    } else {
        return null;
    }
}

/**
 * @function
 * 
 * Compatibility with PayPal Donations gateway
 * Calculate the final price
 * Compatibility with front-end dependent gateways
 * 
 * @return void
 * 
 */
function conversionCurrency() {
    // Attributes needed to convert paypal donations
    let form = document.getElementById(\'give-form-' . $id_prefix . '\');
    let paypalCheckbox = document.getElementById(\'give-gateway-paypal-commerce-' . $id_prefix . '\');
    let currencyCode = document.getElementById(\'give-mc-select\');
    let amountLabel = document.getElementById(\'give-amount\');
    let amount = document.getElementById(\'give-mc-amount\');
    let giveTier = document.getElementsByName(\'give-price-id\')[0];
    let finalAmount = document.getElementsByClassName(\'give-final-total-amount\');

    // Check if paypal donations is selected, is a foreign currency and if there is a conversion fee
    if (exRate !== \'disabled\' && paypalCheckbox.checked && currencyCode.value !== \'BRL\') {

        form.setAttribute(\'data-currency_symbol\', \'R$\');
        form.setAttribute(\'data-currency_code\', \'BRL\');

        // To do the conversion put the converted value hidden from the displayed value
        amountLabel.removeAttribute(\'name\');
        amount.setAttribute(\'name\', \'give-amount\');
        // Remove semicolons to convert
        amount.value = amountLabel.value.replace(/\D/gm, \'\');
        amount.value = amount.value * exRate[currencyCode.value];
        amount.value = Math.round(amount.value);

        // If there is a \'totalPrice\' it changes to \'amount.value\'
        if (finalAmount[0]) {
            finalAmount[0].setAttribute(\'data-total\', amount.value);
        }

        // If there are donation levels, change them for givewp validation
        if (giveTier) {
            giveTier.value = \'custom\';

            // Checks if the converted amount is part of any donation tier
            let tierButtons = document.getElementsByClassName(\'give-donation-level-btn\');
            if (tierButtons) {
                for (let c = 0; c < tierButtons.length; c++) {
                    if (amount.value == tierButtons.item(c).value) {
                        // If the converted value is a donation tier, pass the id of the tier for the give to validate
                        giveTier.value = tierButtons.item(c).getAttribute(\'data-price-id\');
                    }
                    // Catch infinite loop exception
                    if (c > 100) {
                        console.error(\'caught exception infinite loop\');
                        break;
                    }
                }
            }
        }

    } else { // If it is BRL or another gateway other than paypal donations, it does not convert and
        // Reset the donation level for the selected button if it exists
        amountLabel.setAttribute(\'name\', \'give-amount\');
        amount.removeAttribute(\'name\');

        if (currencyCode.value !== \'BRL\') {
            // Remove semicolons to convert
            amount.value = amountLabel.value.replace(/\D/gm, \'\');
            amount.value = amount.value * exRate[currencyCode.value];
            amount.value = parseFloat(amount.value).toFixed(2);

            // If there is a \'totalPrice\' it changes to \'amount.value\'
            if (finalAmount[0]) {
                finalAmount[0].setAttribute(\'data-total\', amount.value);
            }
        }


        if (giveTier) {
            let tierButtons = document.getElementsByClassName(\'give-donation-level-btn\');
            // Checks for donation levels
            if (tierButtons) {
                for (let c = 0; c < tierButtons.length; c++) {
                    // Reset the give-price-id for the selected tier
                    if (amountLabel.value == tierButtons.item(c).value) {
                        giveTier.value = tierButtons.item(c).getAttribute(\'data-price-id\');
                    }
                    // Catch infinite loop exception
                    if (c > 100) {
                        console.error(\'caught exception infinite loop\');
                        break;
                    }
                }
            }
        }
    }

}

/**
 * Compatibility with symbol changes on Classic template
 *
 * @return void
 */
function changeBtnLabelClassic() {
    let currencyCode = document.getElementById(\'give-mc-select\');
    let updateSymbolsFn = function () {
        let classicCurrencyButonsBefore = document.getElementsByClassName(\'give-currency-symbol-before\');
        let classicCurrencyButonsAfter = document.getElementsByClassName(\'give-currency-symbol-after\');
        if (classicCurrencyButonsBefore[0]) {
            for (let c = 0; c < classicCurrencyButonsBefore.length; c++) {
                classicCurrencyButonsBefore[c].textContent = currencySymbolArray[currencyCode.value];
            }
        }
        if (classicCurrencyButonsAfter[0]) {
            for (let c = 0; c < classicCurrencyButonsAfter.length; c++) {
                classicCurrencyButonsAfter[c].textContent = currencySymbolArray[currencyCode.value];
            }
        }
    };

    if (classicBtnIntervalId) {
        clearInterval(classicBtnIntervalId);
        classicBtnIntervalId = setInterval(updateSymbolsFn, 1000);
    } else {
        classicBtnIntervalId = setInterval(updateSymbolsFn, 1000);
    }
}

/**
 * @function
 * 
 * Change the symbol of the amount in Summary template
 * 
 * @return void
 * 
 */
function changeLabelSummary() {
    let updateSymbolsFn = function () {
        let currencyCode = document.getElementById(\'give-mc-select\');
        let giveSummaryAmount = document.getElementsByClassName(\'give-amount-summery\')[0];
        let pastSymbol = giveSummaryAmount.textContent;

        // Cycles through the entire string to find and replace the currency symbol
        for (let c = 0; c < pastSymbol.length; c++) {
            if (/\d/.test(pastSymbol.charAt(c)) === true) {
                pastSymbol = pastSymbol.substring(0, c);
                break;
            }

            if (c > 100) {
                console.error(\'Inifinite loop exception\');
                break;
            }
        }

        // Changes the amount in the summary template
        if (pastSymbol !== giveSummaryAmount.textContent) {
            giveSummaryAmount.textContent = giveSummaryAmount.textContent.replace(pastSymbol, currencySymbolArray[currencyCode.value]);
        }
    };

    if (summaryIntervalId) {
        clearInterval(summaryIntervalId);
        summaryIntervalId = setInterval(updateSymbolsFn, 1000);
    } else {
        summaryIntervalId = setInterval(updateSymbolsFn, 1000);
    }

}

</script>

        <style>
            .lkn-mc-select-classic {
                padding: 13px;
                margin: 0px 50px;
            }
            #give-mc-select {
                font-size: 18px;
            }
            #link-multi-currency {
                justify-content: center;
                align-items: center;
                text-align: center;
                margin: 5px auto;
                font-size:11px;
                font-weight: 600;
                padding: 5px;
            }
            .hidden-lkn {
                display: none;
            }
            .show-lkn {
                display: block;
            }
        </style>

        <input type="hidden" id="give-mc-amount">
        <input type="hidden" id="give-mc-currency-selected" name="give-mc-selected-currency">

        <select id="give-mc-select" class="give-donation-amount" onchange="currencyChange()">

        <option value=' . $mainCurrency . '>' . $mainCurrencyName . '</option>

        </select>

        <a id="link-multi-currency" href="https://www.linknacional.com.br/wordpress/givewp/multimoeda" target="_blank" rel="nofollow">Plugin Multi Moeda</a>

';
        }
        $allowed_html = array(
            'script' => array(
                'type' => true,
                'src' => true
            ),
            'style' => true,
            'input' => array(
                'type' => true,
                'id' => true,
                'name' => true,
                'value' => true,
                'class' => true,
                'onchange' => true
            ),
            'select' => array(
                'id' => true,
                'class' => true,
                'onchange' => true
            ),
            'option' => array(
                'value' => true
            ),
            'a' => array(
                'id' => true,
                'href' => true,
                'target' => true,
                'rel' => true
            )
        );
        
        // Filtra o HTML conforme as regras definidas
        echo wp_kses($html, $allowed_html);
    }
}