<?php
/**
 * Give - Multi Currency Settings Page/Tab
 *
 * @package    Give_Multi_Currency
 * @subpackage Give_Multi_Currency/includes/admin
 * @author     GiveWP <https://givewp.com>
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add setting to exiting section and tab
 * If you want to add setting to existing tab and existing section then find a required filter for setting and add your logic.
 * With current code we are adding a setting field to "General" section of "General" tab
 *
 * @param $settings
 *
 * @return array
 */
function give_multi_currency_add_setting_into_existing_tab($settings) {
    if (!Give_Admin_Settings::is_setting_page('general', 'currency-settings')) {
        return $settings;
    }

    // Make sure you will create your own section or add new setting before array with type 'sectionend' otherwise setting field with not align properly with other setting fields.
    $new_setting = [];
    foreach ($settings as $key => $setting) {
        if ('give_docs_link' === $setting['type']) { // You can use id to compare or create own sub section to add new setting.
            $new_setting[] = [
                'name' => __('Habilitar Multi Moedas', 'give'),
                'id' => 'multi_currency_enabled_setting_field',
                'desc' => __('Ative ou desative o plugin Multi Moedas, esse plugin só funcionará com a moeda real (BRL - R$)'),
                'type' => 'radio',
                'default' => 'disabled',
                'options' => [
                    'enabled' => __('Habilitado', 'give'),
                    'disabled' => __('Desabilitado', 'give'),
                ],
            ];
            // Campos só aparecem caso o radio esteja selecionado e salvo
            if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                $new_setting[] = [
                    'name' => __('Moedas Habilitadas', 'give'),
                    'id' => 'multi_currency_active_currency',
                    'desc' => __('Selecione as moedas que seu formulário irá aceitar'),
                    'type' => 'multicheck',
                    'default' => 1,
                    'options' => [
                        'usd' => __('Dólar Americano ($)', 'give'),
                        'eur' => __('Euro (€)', 'give'),
                        'jpy' => __('Iene (¥)', 'give'),
                        'gbp' => __('Libra esterlina (£)', 'give'),
                    ],
                ];
            }

            // Opção de moeda padrão
            if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                $new_setting[] = [
                    'name' => __('Moeda padrão', 'give'),
                    'id' => 'multi_currency_default_currency',
                    'desc' => __('Selecione a moeda padrão'),
                    'type' => 'radio',
                    'default' => 'BRL',
                    'options' => [
                        'BRL' => __('Real Brasileiro (R$)', 'give'),
                        'USD' => __('Dólar Americano ($)', 'give'),
                        'EUR' => __('Euro (€)', 'give'),
                        'JPY' => __('Iene (¥)', 'give'),
                        'GBP' => __('Libra esterlina (£)', 'give'),
                    ],
                ];
            }

            $new_setting[] = [
                'id' => 'multi_currency',
                'type' => 'sectionend',
            ];
        }

        $new_setting[] = $setting;
    }

    return $new_setting;
}

add_filter('give_get_settings_general', 'give_multi_currency_add_setting_into_existing_tab');
