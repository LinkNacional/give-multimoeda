<?php

namespace Lkn\GiveMultimoedas\Admin;

use Give_Admin_Settings;

/**
 * Add metabox tab to give form data settings.
 *
 * @package     Give
 * @copyright   Copyright (c) 2020, Impress.org
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
final class GiveMultiCurrencyAdmin {
    /**
     * Give_Metabox_Setting_Fields constructor.
     */
    public function __construct() {
        $this->id = 'lkn_multi_currency_fields';
        $this->prefix = '_lkn_multi_currency_';
    }

    public function setup_setting($settings) {
        // Custom metabox settings.
        $settings["{$this->id}_tab"] = array(
            'id' => "{$this->id}_tab",
            'title' => __('Opções de moedas', 'give-multi-currency'),
            'icon-html' => '<span class="dashicons dashicons-money-alt"></span>',
            'fields' => array(
                array(
                    'id' => "{$this->id}_status",
                    'name' => __('Opções globais', 'give-multi-currency'),
                    'type' => 'radio_inline',
                    'desc' => __('Habilitar opções globais para o formulário ou desabilitar para usar as opções definidas no formulário.', 'give-multi-currency'),
                    'options' => array(
                        'enabled' => __('Habilitado', 'give-multi-currency'),
                        'disabled' => __('Desabilitado', 'give-multi-currency'),
                    ),
                    'default' => 'enabled',
                ),
                array(
                    'id' => "{$this->id}_default_currency",
                    'name' => __('Moeda padrão', 'give-multi-currency'),
                    'type' => 'radio',
                    'desc' => __('Selecione a moeda padrão.', 'give-multi-currency'),
                    'options' => array(
                        'BRL' => __('Real Brasileiro (R$)', 'give'),
                        'USD' => __('Dólar Americano ($)', 'give'),
                        'EUR' => __('Euro (€)', 'give'),
                        'JPY' => __('Iene (¥)', 'give'),
                        'GBP' => __('Libra esterlina (£)', 'give'),
                        'SAR' => __('Rial Saudita (ر.س)', 'give')
                    ),
                    'default' => 'BRL',
                ),
                array(
                    'id' => "{$this->id}_active_currency",
                    'name' => __('Moedas Habilitadas', 'give-multi-currency'),
                    'type' => 'multicheck',
                    'desc' => __('Selecione as moedas que seu formulário irá aceitar.', 'give-multi-currency'),
                    'default' => '1',
                    'options' => array(
                        'USD' => __('Dólar Americano ($)', 'give'),
                        'EUR' => __('Euro (€)', 'give'),
                        'JPY' => __('Iene (¥)', 'give'),
                        'GBP' => __('Libra esterlina (£)', 'give'),
                        'SAR' => __('Rial Saudita (ر.س)', 'give')
                    ),
                ),
            ),
        );

        return $settings;
    }

    public function lkn_give_multi_currency_add_setting_into_existing_tab($settings) {
        if ( ! Give_Admin_Settings::is_setting_page('general', 'currency-settings')) {
            return $settings;
        }

        // Make sure you will create your own section or add new setting before array with type 'sectionend' otherwise setting field with not align properly with other setting fields.
        $new_setting = array();
        foreach ($settings as $key => $setting) {
            if ('give_docs_link' === $setting['type']) { // You can use id to compare or create own sub section to add new setting.
                $new_setting[] = array(
                    'name' => __('Habilitar Multi Moedas', 'give'),
                    'id' => 'multi_currency_enabled_setting_field',
                    'desc' => __('Ative ou desative o plugin Multi Moedas, esse plugin só funcionará com a moeda real (BRL - R$)'),
                    'type' => 'radio',
                    'default' => 'disabled',
                    'options' => array(
                        'enabled' => __('Habilitado', 'give'),
                        'disabled' => __('Desabilitado', 'give'),
                    ),
                );
                // Only apears if 'multi_currency_enabled_setting_field' is 'enabled'
                if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                    $new_setting[] = array(
                        'name' => __('Moedas Habilitadas', 'give'),
                        'id' => 'multi_currency_active_currency',
                        'desc' => __('Selecione as moedas que seu formulário irá aceitar') . '<br><a href="https://www.linknacional.com.br/wordpress/givewp/multimoeda/#nova-moeda" target="_blank">Adicionar nova moeda</a>',
                        'type' => 'multicheck',
                        'default' => 1,
                        'options' => array(
                            'usd' => __('Dólar Americano ($)', 'give'),
                            'eur' => __('Euro (€)', 'give'),
                            'jpy' => __('Iene (¥)', 'give'),
                            'gbp' => __('Libra esterlina (£)', 'give'),
                            'sar' => __('Rial Saudita (ر.س)', 'give'),
                            'mxn' => __('Peso mexicano ($)', 'give')
                        ),
                    );
                }

                // Default currency option
                if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                    $new_setting[] = array(
                        'name' => __('Moeda padrão', 'give'),
                        'id' => 'multi_currency_default_currency',
                        'desc' => __('Selecione a moeda padrão'),
                        'type' => 'radio',
                        'default' => 'BRL',
                        'options' => array(
                            'BRL' => __('Real Brasileiro (R$)', 'give'),
                            'USD' => __('Dólar Americano ($)', 'give'),
                            'EUR' => __('Euro (€)', 'give'),
                            'JPY' => __('Iene (¥)', 'give'),
                            'GBP' => __('Libra esterlina (£)', 'give'),
                            'SAR' => __('Rial Saudita (ر.س)', 'give'),
                            'MXN' => __('Peso mexicano ($)', 'give')
                        ),
                    );
                }

                $new_setting[] = array(
                    'id' => 'multi_currency',
                    'type' => 'sectionend',
                );
            }

            $new_setting[] = $setting;
        }

        return $new_setting;
    }

    /**
     * Add a form admin notice
     *
     * @return string $html
     *
     */
    public function disabled_for_non_legacy_templates_html() {
        ob_start(); ?>
<p class="ffconfs-disabled">
    <?php esc_attr('O formulário customizado não é relevante para o formulário Multi-Step do giveWP. Caso você deseje utilizar o Free Form Plugin é necessário mudar o Template do formulário para opção "Legado".', 'give-multi-currency-notices'); ?>
</p>
<?php

        $html = ob_get_contents();

        return $html;
    }
}
?>