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
        $this->id = 'lkn_multi_currency_fields';
        $this->prefix = '_lkn_multi_currency_';
    }

    public function setup_setting($settings)
    {
        // Custom metabox settings.
        $settings["{$this->id}_tab"] = array(
            'id' => "{$this->id}_tab",
            'title' => __('Currency Options', 'multi-currency-for-give'),
            'icon-html' => '<span class="dashicons dashicons-money-alt"></span>',
            'fields' => array(
                array(
                    'id' => "{$this->id}_status",
                    'name' => __('Global Options', 'multi-currency-for-give'),
                    'type' => 'radio_inline',
                    'desc' => __('Enable global options for the form or disable to use form-specific options.', 'multi-currency-for-give'),
                    'options' => array(
                        'enabled' => __('Enabled', 'multi-currency-for-give'),
                        'disabled' => __('Disabled', 'multi-currency-for-give'),
                    ),
                    'default' => 'enabled',
                ),
                array(
                    'id' => "{$this->id}_default_currency",
                    'name' => __('Default Currency', 'multi-currency-for-give'),
                    'type' => 'radio',
                    'desc' => __('Select the default currency.', 'multi-currency-for-give'),
                    'options' => array(
                        'BRL' => __('Brazilian Real (R$)', 'multi-currency-for-give'),
                        'USD' => __('US Dollar ($)', 'multi-currency-for-give'),
                        'EUR' => __('Euro (€)', 'multi-currency-for-give'),
                        'JPY' => __('Japanese Yen (¥)', 'multi-currency-for-give'),
                        'GBP' => __('British Pound (£)', 'multi-currency-for-give'),
                        'SAR' => __('Saudi Riyal (ر.س)', 'multi-currency-for-give'),
                        'MXN' => __('Mexican Peso ($)', 'multi-currency-for-give'),
                        'CHF' => __('Swiss Franc (CHF)', 'multi-currency-for-give')
                    ),
                    'default' => 'BRL',
                ),
                array(
                    'id' => "{$this->id}_active_currency",
                    'name' => __('Enabled Currencies', 'multi-currency-for-give'),
                    'type' => 'multicheck',
                    'desc' => __('Select the currencies your form will accept.', 'multi-currency-for-give'),
                    'default' => '1',
                    'options' => array(
                        'BRL' => __('Brazilian Real (R$)', 'multi-currency-for-give'),
                        'USD' => __('US Dollar ($)', 'multi-currency-for-give'),
                        'EUR' => __('Euro (€)', 'multi-currency-for-give'),
                        'JPY' => __('Japanese Yen (¥)', 'multi-currency-for-give'),
                        'GBP' => __('British Pound (£)', 'multi-currency-for-give'),
                        'SAR' => __('Saudi Riyal (ر.س)', 'multi-currency-for-give'),
                        'MXN' => __('Mexican Peso ($)', 'multi-currency-for-give'),
                        'CHF' => __('Swiss Franc (CHF)', 'multi-currency-for-give')
                    ),
                ),
            ),
        );

        return $settings;
    }

    public function lkn_give_multi_currency_add_setting_into_existing_tab($settings)
    {
        if (! Give_Admin_Settings::is_setting_page('general', 'currency-settings')) {
            return $settings;
        }

        // Make sure you will create your own section or add new setting before array with type 'sectionend' otherwise setting field with not align properly with other setting fields.
        $new_setting = array();
        foreach ($settings as $key => $setting) {
            if ('give_docs_link' === $setting['type']) { // You can use id to compare or create own sub section to add new setting.
                $new_setting[] = array(
                    'name' => __('Enable Multi Currency', 'multi-currency-for-give'),
                    'id' => 'multi_currency_enabled_setting_field',
                    'desc' => __('Enable or disable the Multi Currency plugin. This plugin only works with Brazilian Real (BRL - R$)', 'multi-currency-for-give'),
                    'type' => 'radio',
                    'default' => 'disabled',
                    'options' => array(
                        'enabled' => __('Enabled', 'multi-currency-for-give'),
                        'disabled' => __('Disabled', 'multi-currency-for-give'),
                    ),
                );
                // Only apears if 'multi_currency_enabled_setting_field' is 'enabled'
                if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                    $new_setting[] = array(
                        'name' => __('Enabled Currencies', 'multi-currency-for-give'),
                        'id' => 'multi_currency_active_currency',
                        'desc' => __('Select the currencies your form will accept', 'multi-currency-for-give') . '<br><a href="https://www.linknacional.com.br/wordpress/givewp/multimoeda/#nova-moeda" target="_blank">' . __('Add new currency', 'multi-currency-for-give') . '</a>',
                        'type' => 'multicheck',
                        'default' => 1,
                        'options' => array(
                            'brl' => __('Brazilian Real (R$)', 'multi-currency-for-give'),
                            'usd' => __('US Dollar ($)', 'multi-currency-for-give'),
                            'eur' => __('Euro (€)', 'multi-currency-for-give'),
                            'jpy' => __('Japanese Yen (¥)', 'multi-currency-for-give'),
                            'gbp' => __('British Pound (£)', 'multi-currency-for-give'),
                            'sar' => __('Saudi Riyal (ر.س)', 'multi-currency-for-give'),
                            'mxn' => __('Mexican Peso ($)', 'multi-currency-for-give'),
                            'chf' => __('Swiss Franc (CHF)', 'multi-currency-for-give')
                        ),
                    );
                }

                // Default currency option
                if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                    $new_setting[] = array(
                        'name' => __('Default Currency', 'multi-currency-for-give'),
                        'id' => 'multi_currency_default_currency',
                        'desc' => __('Select the default currency', 'multi-currency-for-give'),
                        'type' => 'radio',
                        'default' => 'BRL',
                        'options' => array(
                            'BRL' => __('Brazilian Real (R$)', 'multi-currency-for-give'),
                            'USD' => __('US Dollar ($)', 'multi-currency-for-give'),
                            'EUR' => __('Euro (€)', 'multi-currency-for-give'),
                            'JPY' => __('Japanese Yen (¥)', 'multi-currency-for-give'),
                            'GBP' => __('British Pound (£)', 'multi-currency-for-give'),
                            'SAR' => __('Saudi Riyal (ر.س)', 'multi-currency-for-give'),
                            'MXN' => __('Mexican Peso ($)', 'multi-currency-for-give'),
                            'CHF' => __('Swiss Franc (CHF)', 'multi-currency-for-give')
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
    <?php esc_html_e('Custom form is not relevant for GiveWP Multi-Step forms. If you want to use the Free Form Plugin, you need to change the form template to "Legacy" option.', 'multi-currency-for-give'); ?>
</p>
<?php

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
?>