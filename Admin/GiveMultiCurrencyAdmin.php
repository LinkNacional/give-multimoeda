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
final class GiveMultiCurrencyAdmin
{
    /**
     * Admin ID
     *
     * @var string
     */
    private $id;

    /**
     * Admin prefix
     *
     * @var string
     */
    private $prefix;

    /**
     * Give_Metabox_Setting_Fields constructor.
     */
    public function __construct()
    {
        $this->id = 'lknaci_mcfg__fields';
        $this->prefix = '_lknaci_mcfg__';
    }

    public function setup_setting($settings)
    {
        // Custom metabox settings.
        $settings["{$this->id}_tab"] = array(
            'id' => "{$this->id}_tab",
            'title' => __('Currency Options', 'lknaci-multi-currency-for-givewp'),
            'icon-html' => '<span class="dashicons dashicons-money-alt"></span>',
            'fields' => array(
                array(
                    'id' => "{$this->id}_status",
                    'name' => __('Global Options', 'lknaci-multi-currency-for-givewp'),
                    'type' => 'radio_inline',
                    'desc' => __('Enable global options for the form or disable to use form-specific options.', 'lknaci-multi-currency-for-givewp'),
                    'options' => array(
                        'enabled' => __('Enabled', 'lknaci-multi-currency-for-givewp'),
                        'disabled' => __('Disabled', 'lknaci-multi-currency-for-givewp'),
                    ),
                    'default' => 'enabled',
                ),
                array(
                    'id' => "{$this->id}_default_currency",
                    'name' => __('Default Currency', 'lknaci-multi-currency-for-givewp'),
                    'type' => 'radio',
                    'desc' => __('Select the default currency.', 'lknaci-multi-currency-for-givewp'),
                    'options' => array(
                        'BRL' => __('Brazilian Real (R$)', 'lknaci-multi-currency-for-givewp'),
                        'USD' => __('US Dollar ($)', 'lknaci-multi-currency-for-givewp'),
                        'EUR' => __('Euro (€)', 'lknaci-multi-currency-for-givewp'),
                        'JPY' => __('Japanese Yen (¥)', 'lknaci-multi-currency-for-givewp'),
                        'GBP' => __('British Pound (£)', 'lknaci-multi-currency-for-givewp'),
                        'SAR' => __('Saudi Riyal (ر.س)', 'lknaci-multi-currency-for-givewp'),
                        'MXN' => __('Mexican Peso ($)', 'lknaci-multi-currency-for-givewp'),
                        'CHF' => __('Swiss Franc (CHF)', 'lknaci-multi-currency-for-givewp')
                    ),
                    'default' => 'BRL',
                ),
                array(
                    'id' => "{$this->id}_active_currency",
                    'name' => __('Enabled Currencies', 'lknaci-multi-currency-for-givewp'),
                    'type' => 'multicheck',
                    'desc' => __('Select the currencies your form will accept.', 'lknaci-multi-currency-for-givewp'),
                    'default' => '1',
                    'options' => array(
                        'BRL' => __('Brazilian Real (R$)', 'lknaci-multi-currency-for-givewp'),
                        'USD' => __('US Dollar ($)', 'lknaci-multi-currency-for-givewp'),
                        'EUR' => __('Euro (€)', 'lknaci-multi-currency-for-givewp'),
                        'JPY' => __('Japanese Yen (¥)', 'lknaci-multi-currency-for-givewp'),
                        'GBP' => __('British Pound (£)', 'lknaci-multi-currency-for-givewp'),
                        'SAR' => __('Saudi Riyal (ر.س)', 'lknaci-multi-currency-for-givewp'),
                        'MXN' => __('Mexican Peso ($)', 'lknaci-multi-currency-for-givewp'),
                        'CHF' => __('Swiss Franc (CHF)', 'lknaci-multi-currency-for-givewp')
                    ),
                ),
            ),
        );

        return $settings;
    }

    public function lknaci_mcfg__add_setting_into_existing_tab($settings)
    {
        if (! Give_Admin_Settings::is_setting_page('general', 'currency-settings')) {
            return $settings;
        }

        // Make sure you will create your own section or add new setting before array with type 'sectionend' otherwise setting field with not align properly with other setting fields.
        $new_setting = array();
        foreach ($settings as $key => $setting) {
            if ('give_docs_link' === $setting['type']) { // You can use id to compare or create own sub section to add new setting.
                $new_setting[] = array(
                    'name' => __('Enable Multi Currency', 'lknaci-multi-currency-for-givewp'),
                    'id' => 'multi_currency_enabled_setting_field',
                    'desc' => __('Enable or disable the Multi Currency plugin. This plugin only works with Brazilian Real (BRL - R$)', 'lknaci-multi-currency-for-givewp'),
                    'type' => 'radio',
                    'default' => 'disabled',
                    'options' => array(
                        'enabled' => __('Enabled', 'lknaci-multi-currency-for-givewp'),
                        'disabled' => __('Disabled', 'lknaci-multi-currency-for-givewp'),
                    ),
                );
                // Only apears if 'multi_currency_enabled_setting_field' is 'enabled'
                if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                    $new_setting[] = array(
                        'name' => __('Enabled Currencies', 'lknaci-multi-currency-for-givewp'),
                        'id' => 'multi_currency_active_currency',
                        'desc' => __('Select the currencies your form will accept', 'lknaci-multi-currency-for-givewp') . '<br><a href="https://www.linknacional.com.br/wordpress/givewp/multimoeda/#nova-moeda" target="_blank">' . __('Add new currency', 'lknaci-multi-currency-for-givewp') . '</a>',
                        'type' => 'multicheck',
                        'default' => 1,
                        'options' => array(
                            'brl' => __('Brazilian Real (R$)', 'lknaci-multi-currency-for-givewp'),
                            'usd' => __('US Dollar ($)', 'lknaci-multi-currency-for-givewp'),
                            'eur' => __('Euro (€)', 'lknaci-multi-currency-for-givewp'),
                            'jpy' => __('Japanese Yen (¥)', 'lknaci-multi-currency-for-givewp'),
                            'gbp' => __('British Pound (£)', 'lknaci-multi-currency-for-givewp'),
                            'sar' => __('Saudi Riyal (ر.س)', 'lknaci-multi-currency-for-givewp'),
                            'mxn' => __('Mexican Peso ($)', 'lknaci-multi-currency-for-givewp'),
                            'chf' => __('Swiss Franc (CHF)', 'lknaci-multi-currency-for-givewp')
                        ),
                    );
                }

                // Default currency option
                if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                    $new_setting[] = array(
                        'name' => __('Default Currency', 'lknaci-multi-currency-for-givewp'),
                        'id' => 'multi_currency_default_currency',
                        'desc' => __('Select the default currency', 'lknaci-multi-currency-for-givewp'),
                        'type' => 'radio',
                        'default' => 'BRL',
                        'options' => array(
                            'BRL' => __('Brazilian Real (R$)', 'lknaci-multi-currency-for-givewp'),
                            'USD' => __('US Dollar ($)', 'lknaci-multi-currency-for-givewp'),
                            'EUR' => __('Euro (€)', 'lknaci-multi-currency-for-givewp'),
                            'JPY' => __('Japanese Yen (¥)', 'lknaci-multi-currency-for-givewp'),
                            'GBP' => __('British Pound (£)', 'lknaci-multi-currency-for-givewp'),
                            'SAR' => __('Saudi Riyal (ر.س)', 'lknaci-multi-currency-for-givewp'),
                            'MXN' => __('Mexican Peso ($)', 'lknaci-multi-currency-for-givewp'),
                            'CHF' => __('Swiss Franc (CHF)', 'lknaci-multi-currency-for-givewp')
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
    public function disabled_for_non_legacy_templates_html()
    {
        ob_start(); ?>
<p class="ffconfs-disabled">
    <?php esc_html_e('Custom form is not relevant for GiveWP Multi-Step forms. If you want to use the Free Form Plugin, you need to change the form template to "Legacy" option.', 'lknaci-multi-currency-for-givewp'); ?>
</p>
<?php

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
?>