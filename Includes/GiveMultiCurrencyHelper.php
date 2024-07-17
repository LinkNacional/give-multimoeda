<?php

namespace Lkn\GiveMultimoedas\Includes;

final class GiveMultiCurrencyHelper {
    /**
     * Show plugin dependency notice
     *
     * @since
     */
    public static function lkn_give_multi_currency_dependency_notice(): void {
        // Admin notice.
        $message = sprintf(
            '<strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a>  %5$s %6$s+ %7$s.',
            __('Activation Error:', 'give'),
            __('You must have', 'give'),
            'https://givewp.com',
            __('Give', 'give'),
            __('version', 'give'),
            GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION,
            __('for the Give Multi Currency add-on to activate', 'give')
        );
        $message = wp_kses_post( $message );
        Give()->notices->register_notice(array(
            'id' => 'give-activation-error',
            'type' => 'error',
            'description' => $message,
            'show' => true,
        ));
    }

    /**
     * Notice for No Core Activation
     *
     * @since
     */
    public static function lkn_give_multi_currency_inactive_notice(): void {
        // Admin notice.
        $message = sprintf(
            '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s.</p></div>',
            __('Activation Error:', 'give'),
            __('You must have', 'give'),
            'https://givewp.com',
            __('Give', 'give'),
            __(' plugin installed and activated for the Give Multi Currency to activate', 'give')
        );

        echo $message;
    }

    /**
     * Plugin row meta links.
     *
     * @since
     *
     * @param array $plugin_meta An array of the plugin's metadata.
     * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
     *
     * @return array
     */
    public static function lkn_give_multi_currency_plugin_row_meta($plugin_meta, $plugin_file) {
        $new_meta_links['setting'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            admin_url('edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings'),
            __('Settings', 'give')
        );

        return array_merge($plugin_meta, $new_meta_links);
    }

    /**
     * Show activation banner
     *
     * @since
     * @return void
     */
    public static function lkn_give_multi_currency_activation(): void {
        // Initialize activation welcome banner.
        if (class_exists('Give_Multi_Currency')) {
            // Only runs on admin.
            $args = array(
                'file' => GIVE_MULTI_CURRENCY_FILE,
                'name' => __('Multi Currency', 'give'),
                'version' => GIVE_MULTI_CURRENCY_VERSION,
                'settings_url' => admin_url('edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings'),
                'documentation_url' => 'https://www.linknacional.com.br/wordpress/givewp/',
                'support_url' => 'https://www.linknacional.com.br/suporte/',
                'testing' => false, // Never leave true.
            );

            new GiveMultiCurrency($args);
        }
    }

    public static function lkn_give_multi_currency_get_exchange_rates($currenciesCode) {
        $exRate = array();

        foreach ($currenciesCode as $key => $currency) {
            $result = self::lkn_multi_currency_curl_get_contents('https://api.linknacional.com/cotacao/cotacao-' . $currency . '.json');
            $result = wp_json_file_decode($result);
            $exRate[$currency] = $result->rates->BRL;
        }

        // retorna um array com o rate das moedas ativas
        return wp_json_encode($exRate);
    }

    /**
     * Gets all active currencies symbols
     *
     * @param array $currenciesCode
     *
     * @return string $currenciesSymbol
     *
     */
    public static function lkn_give_multi_currency_get_symbols($currenciesCode) {
        $currenciesSymbol['BRL'] = 'R$';

        if ( ! empty($currenciesCode)) {
            for ($c = 0; $c < count($currenciesCode); $c++) {
                $currenciesSymbol[$currenciesCode[$c]] = give_currency_symbol($currenciesCode[$c], true);
            }
        }

        return wp_json_encode($currenciesSymbol);
    }

    /**
     * Query a new request
     *
     * @param string $url
     *
     * @return string $response
     *
     */
    public static function lkn_multi_currency_curl_get_contents($url) {
        // $ch = curl_init();

        // curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_URL, $url);

        // $data = curl_exec($ch);
        // curl_close($ch);
        $data = wp_remote_get($url);

        return $data;
    }

    public static function __lkn_multicurrency_linkn_inactive_notice(): void {
        $message = '<div id="message" class="error"><p><b>Atenção: </b>O plugin Give Multimoedas detectou que o plugin Cielo API 3.0 encontra-se inativo. <a href="plugins.php">Ativar na área de plugins</a>.</div>';
        echo $message;
    }
}
