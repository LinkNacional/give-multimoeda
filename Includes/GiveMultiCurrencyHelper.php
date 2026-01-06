<?php

namespace Lkn\GiveMultimoedas\Includes;

final class GiveMultiCurrencyHelper {
    /**
     * Show plugin dependency notice
     *
     * @since
     */
    public static function lknaci_mcfg__dependency_notice(): void {
        // Admin notice.
        $message = sprintf(
            '<strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a>  %5$s %6$s+ %7$s.',
            __('Activation Error:', 'lknaci-multi-currency-for-givewp'),
            __('You must have', 'lknaci-multi-currency-for-givewp'),
            'https://givewp.com',
            __('Give', 'lknaci-multi-currency-for-givewp'),
            __('version', 'lknaci-multi-currency-for-givewp'),
            GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION,
            __('for the Give Multi Currency add-on to activate', 'lknaci-multi-currency-for-givewp')
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
    public static function lknaci_mcfg__inactive_notice(): void {
        // Admin notice.
        $message = sprintf(
            '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s.</p></div>',
            __('Activation Error:', 'lknaci-multi-currency-for-givewp'),
            __('You must have', 'lknaci-multi-currency-for-givewp'),
            'https://givewp.com',
            __('Give', 'lknaci-multi-currency-for-givewp'),
            __(' plugin installed and activated for the Give Multi Currency to activate', 'lknaci-multi-currency-for-givewp')
        );

        echo wp_kses_post($message);
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
    public static function lknaci_mcfg__plugin_row_meta($plugin_meta, $plugin_file) {
        $new_meta_links['setting'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            admin_url('edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings'),
            __('Settings', 'lknaci-multi-currency-for-givewp')
        );

        return array_merge($plugin_meta, $new_meta_links);
    }

    /**
     * Show activation banner
     *
     * @since
     * @return void
     */
    public static function lknaci_mcfg__activation(): void {
        // Initialize activation welcome banner.
        if (class_exists('Give_Multi_Currency')) {
            // Only runs on admin.
            $args = array(
                'file' => GIVE_MULTI_CURRENCY_FILE,
                'name' => __('Multi Currency', 'lknaci-multi-currency-for-givewp'),
                'version' => GIVE_MULTI_CURRENCY_VERSION,
                'settings_url' => admin_url('edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings'),
                'documentation_url' => 'https://www.linknacional.com.br/wordpress/givewp/',
                'support_url' => 'https://www.linknacional.com.br/suporte/',
                'testing' => false, // Never leave true.
            );

            new GiveMultiCurrency($args);
        }
    }

    /**
     * Get exchange rates using WordPress HTTP API with caching
     */
    public static function lknaci_mcfg__get_exchange_rates($currenciesCode) {
        $exRate = array();

        foreach ($currenciesCode as $key => $currency) {
            // Try to get cached rate first
            $cached_rate = get_transient('lknaci_mcfg_rate_' . $currency);
            
            if (false !== $cached_rate) {
                $exRate[$currency] = $cached_rate;
                continue;
            }

            // Fetch from API using WordPress HTTP functions
            $response = wp_remote_get('https://api.linknacional.com/cotacao/cotacao-' . $currency . '.json');
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $body = wp_remote_retrieve_body($response);
                $result = json_decode($body, true);
                
                if (isset($result['rates']['BRL'])) {
                    $rate = $result['rates']['BRL'];
                    $exRate[$currency] = $rate;
                    
                    // Cache the rate for 1 hour
                    set_transient('lknaci_mcfg_rate_' . $currency, $rate, HOUR_IN_SECONDS);
                }
            }
        }

        // returns an array with the exchange rates of active currencies
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
    public static function lknaci_mcfg__get_symbols($currenciesCode) {
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
    public static function lknaci_mcfg__curl_get_contents($url) {
        $data = wp_remote_get($url);

        return $data;
    }

    public static function __lkn_multicurrency_linkn_inactive_notice(): void {
        $message = sprintf(
            '<div id="message" class="error"><p><b>%1$s</b> %2$s <a href="plugins.php">%3$s</a>.</div>',
            __('Attention:', 'lknaci-multi-currency-for-givewp'),
            __('The Give Multi Currency plugin detected that the Cielo API 3.0 plugin is inactive.', 'lknaci-multi-currency-for-givewp'),
            __('Activate in plugins area', 'lknaci-multi-currency-for-givewp')
        );
        echo wp_kses_post($message);
    }

    public static function give_multi_currency_check_cielo(): void {
        if ( ! function_exists('get_plugins') || ! function_exists('is_plugin_active')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }
        if (is_plugin_inactive('give-cielo/lkn-give-cielo.php')) {
            $lkn_multicurrency_all_plugins = get_plugins();

            if (isset($lkn_multicurrency_all_plugins['give-cielo/lkn-give-cielo.php']) && ! isset($lkn_multicurrency_all_plugins['give-cielo/give-cielo.php'])) {
                add_action('admin_notices', array('Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyHelper', '__lkn_multicurrency_linkn_inactive_notice'));
            }
        }
    }
}
