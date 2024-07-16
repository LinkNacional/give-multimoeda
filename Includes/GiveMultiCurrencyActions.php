<?php

namespace Lkn\GiveMultimoedas\Includes;

use Give\DonationForms\Models\DonationForm;
use Give\Donations\Models\Donation;
use Give_Payment;
use Give\Framework\FieldsAPI\DonationForm as DonationFormNode;
use Give\Framework\FieldsAPI\Properties\DonationForm\CurrencySwitcherSetting;

// Exit, if accessed directly.
if ( ! defined('ABSPATH')) {
    exit;
}

final class GiveMultiCurrencyActions {
    //  FrontEnd
    public static function give_import_script_method(): void {
        wp_enqueue_script("lkn-multi-currency-coin", GIVE_MULTI_CURRENCY_URL . "/resource/give-multi-currency-coin-selector.js");
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
        add_option(uniqid("Lkn_Post"), json_encode($_POST));
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

        ?>

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
        font-size: 11px;
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

    <option value=<?php echo esc_html($mainCurrency)?> simbol=<?php echo esc_attr(give_currency_symbol($mainCurrency))?>><?php echo esc_html($mainCurrencyName)?>
    </option>

    <?php foreach($configs['activeCurrency'] as $currency): ?>
    <option value=<?php echo esc_attr($currency)?> simbol=<?php echo esc_attr(give_currency_symbol($currency))?>><?php echo esc_html(give_get_currency_name($currency))?>
    </option>

    <?php endforeach; ?>
</select>



<a
    id="link-multi-currency"
    href="https://www.linknacional.com.br/wordpress/givewp/multimoeda"
    target="_blank"
    rel="nofollow"
>Plugin Multi Moeda</a>

<?php

    }

    // GiveWp 3.0.0

    public function lkn_add_currency_selector_to_give_form(DonationFormNode $form, $formId): void {
        $donationForm = DonationForm::find($formId);
        $gateways = $this->lkn_get_gateways($formId);
        //Moedas Habilitadas
        $adminCurrency = self::lkn_give_multi_currency_get_active_currency();
        // Moeda Padrão
        $standardCurrency = give_get_option("multi_currency_default_currency");
        $currencySettings = array();

        //Adicionando as moedas Habilitadas no formulário

        foreach ($adminCurrency as $currency) {
            $currencySettings[] = new CurrencySwitcherSetting($currency, 1, $gateways);
        }
        $currencySettings[] = new CurrencySwitcherSetting(strtoupper($standardCurrency), 1, $gateways);

        $form->currencySwitcherSettings(...$currencySettings);
        //Script para escolher a moeda
        wp_enqueue_script("lkn-multi-edit-coin", GIVE_MULTI_CURRENCY_URL . "resource/index.js");
        wp_localize_script("lkn-multi-edit-coin", "varsPhp", array(
            "standardCurrency" => $standardCurrency
        ));
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