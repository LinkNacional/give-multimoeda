<?php

namespace Lkn\GiveMultimoedas\Includes;

use Give\Framework\FieldsAPI\DonationForm as DonationFormNode;
use Give\Framework\FieldsAPI\Properties\DonationForm\CurrencySwitcherSetting;

// Exit, if accessed directly.
if ( ! defined('ABSPATH')) {
    exit;
}

final class GiveMultiCurrencyActions {
    //  FrontEnd
    public static function give_import_script_method(): void {
        wp_enqueue_script("lkn-multi-currency-coin", GIVE_MULTI_CURRENCY_URL . "resource/give-multi-currency-coin-selector.js");
        $configs = self::lkn_give_multi_currency_get_configs();
        $currency = GiveMultiCurrencyHelper::lkn_give_multi_currency_get_symbols($configs["activeCurrency"]);
        wp_localize_script("lkn-multi-currency-coin", "varsPhp", array("moedas" => $currency));
    }

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
        $configs = self::lkn_give_multi_currency_get_configs();
        if ("enabled" == $configs["mcEnabled"]) {
            if ( ! empty($_POST['give-mc-selected-currency']) && wp_verify_nonce($_POST['lkn-give-multi-nonce'], 'lkn-give-multi-currency-nonce') && 'paypal-commerce' !== $_POST['payment-mode']) {
                $currency = $_POST['give-mc-selected-currency'];
            } elseif (isset($_POST["currency"])) {
                $currency = $_POST['currency'];
            }
        }
        return $currency;
    }

    public static function lkn_give_multi_currency_thousand_separator($separator) {
        $configs = self::lkn_give_multi_currency_get_configs();

        if ("enabled" == $configs["mcEnabled"]) {
            $separator = ".";
        }

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
        $configs = self::lkn_give_multi_currency_get_configs();

        // Verifica se a funcionalidade de multi-moeda está habilitada
        if ("enabled" == $configs["mcEnabled"]) {
            $separator = ",";
        }

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
        $configs = self::lkn_give_multi_currency_get_configs();

        if ("enabled" == $configs["mcEnabled"]) {
            $count = 0;
        }
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
        $configs = self::lkn_give_multi_currency_get_configs();
        $pluginEnabled = $configs['mcEnabled'];
        $mainCurrency = $configs['mainCurrency'];
        $mainCurrencyName = give_get_currency_name($mainCurrency);
        if ("enabled" == $pluginEnabled) {
            ?>

<style>
   .lkn-mc-select-classic {
        padding: 20px;
        text-align: center;
        margin: 0 auto;
    }

    #give-mc-select {
        font-size: 18px;
        display: block; 
        margin: 0 auto;
        max-width: 300px;
        padding: 10px;
    }

    #link-multi-currency {
        display: block;
        text-align: center;
        font-size: 17px;
        font-weight: 600;
        padding: 5px 10px;
        margin: 5px 0px 15px 0px;
        color: #0073e6;
        text-decoration: none;
        position: relative;
        transition: color 0.3s, transform 0.3s;
    }

    #link-multi-currency::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background-color: #0073e6;
        transition: width 0.3s, background-color 0.3s;
        transform: translateX(-50%);
    }

    #link-multi-currency:hover::after,
    #link-multi-currency:focus::after {
        width: 35%;
        background-color: #005bb5;
    }

    #link-multi-currency:hover,
    #link-multi-currency:focus {
        color: #005bb5;
        transform: translateY(-2px);
    }

    .hidden-lkn {
        display: none;
    }

    .show-lkn {
        display: block;
    }
</style>

<input
    type="hidden"
    id="give-mc-amount"
>
<input
    type="hidden"
    id="give-mc-currency-selected"
    name="give-mc-selected-currency"
>

<select
    id="give-mc-select"
    class="give-donation-amount"
>
    <option value=<?php echo esc_html($mainCurrency) ?>
        simbol=<?php echo esc_attr(give_currency_symbol($mainCurrency)) ?>><?php echo esc_html($mainCurrencyName) ?>
    </option>
    <?php foreach ($configs['activeCurrency'] as $currency) : ?>
    <option value=<?php echo esc_attr($currency) ?>
        simbol=<?php echo esc_attr(give_currency_symbol($currency)) ?>><?php echo esc_html(give_get_currency_name($currency)) ?>
    </option>

    <?php endforeach; ?>
</select>
<?php wp_nonce_field('lkn-give-multi-currency-nonce', 'lkn-give-multi-nonce'); ?>
<a
    id="link-multi-currency"
    href="https://www.linknacional.com.br/wordpress/givewp/multimoeda"
    target="_blank"
    rel="nofollow"
>Plugin Multi Moeda</a>

<?php
        }
    }

    // GiveWp 3.0.0

    public function lkn_add_currency_selector_to_give_form(DonationFormNode $form, $formId): void {
        $configs = self::lkn_give_multi_currency_get_configs();
        if ("enabled" == $configs["mcEnabled"]) {
            $gateways = $this->lkn_get_gateways($form);
            //Moedas Habilitadas
            $adminCurrency = self::lkn_give_multi_currency_get_active_currency();
            // Moeda Padrão
            $standardCurrency = give_get_option("multi_currency_default_currency");
            $currencySettings = array();

            //Adicionando as moedas Habilitadas no formulário
            $currencySettings[] = new CurrencySwitcherSetting(strtoupper($standardCurrency), 1, $gateways);

            foreach ($adminCurrency as $currency) {
                $currencySettings[] = new CurrencySwitcherSetting($currency, 1, $gateways);
            }

            $form->currencySwitcherSettings(...$currencySettings);
        }
    }

    //Pegar os gateways no formato desejado
    public function lkn_get_gateways($formId): array {
        $gateways = give_get_enabled_payment_gateways($formId);
        $gatewaysR = array();
        foreach ($gateways as $gateway => $item) {
            $gatewaysR[] = $gateway;
        }
        if (empty($gatewaysR)) {
            return array();
        }

        return $gatewaysR;
    }
}
?>