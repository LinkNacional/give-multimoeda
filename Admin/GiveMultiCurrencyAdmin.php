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
            'title' => __('Currency Options', 'give-multi-currency'),
            'icon-html' => '<span class="dashicons dashicons-money-alt"></span>',
            'fields' => array(
                array(
                    'id' => "{$this->id}_status",
                    'name' => __('Global Options', 'give-multi-currency'),
                    'type' => 'radio_inline',
                    'desc' => __('Enable global options for the form or disable to use form-specific options.', 'give-multi-currency'),
                    'options' => array(
                        'enabled' => __('Enabled', 'give-multi-currency'),
                        'disabled' => __('Disabled', 'give-multi-currency'),
                    ),
                    'default' => 'enabled',
                ),
                array(
                    'id' => "{$this->id}_default_currency",
                    'name' => __('Default Currency', 'give-multi-currency'),
                    'type' => 'radio',
                    'desc' => __('Select the default currency.', 'give-multi-currency'),
                    'options' => array(
                        'BRL' => __('Brazilian Real (R$)', 'give-multi-currency'),
                        'USD' => __('US Dollar ($)', 'give-multi-currency'),
                        'EUR' => __('Euro (€)', 'give-multi-currency'),
                        'JPY' => __('Japanese Yen (¥)', 'give-multi-currency'),
                        'GBP' => __('British Pound (£)', 'give-multi-currency'),
                        'SAR' => __('Saudi Riyal (ر.س)', 'give-multi-currency'),
                        'MXN' => __('Mexican Peso ($)', 'give-multi-currency'),
                        'CHF' => __('Swiss Franc (CHF)', 'give-multi-currency')
                    ),
                    'default' => 'BRL',
                ),
                array(
                    'id' => "{$this->id}_active_currency",
                    'name' => __('Enabled Currencies', 'give-multi-currency'),
                    'type' => 'multicheck',
                    'desc' => __('Select the currencies your form will accept.', 'give-multi-currency'),
                    'default' => '1',
                    'options' => array(
                        'BRL' => __('Brazilian Real (R$)', 'give-multi-currency'),
                        'USD' => __('US Dollar ($)', 'give-multi-currency'),
                        'EUR' => __('Euro (€)', 'give-multi-currency'),
                        'JPY' => __('Japanese Yen (¥)', 'give-multi-currency'),
                        'GBP' => __('British Pound (£)', 'give-multi-currency'),
                        'SAR' => __('Saudi Riyal (ر.س)', 'give-multi-currency'),
                        'MXN' => __('Mexican Peso ($)', 'give-multi-currency'),
                        'CHF' => __('Swiss Franc (CHF)', 'give-multi-currency')
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
                    'name' => __('Enable Multi Currency', 'give-multi-currency'),
                    'id' => 'multi_currency_enabled_setting_field',
                    'desc' => __('Enable or disable the Multi Currency plugin. This plugin only works with Brazilian Real (BRL - R$)', 'give-multi-currency'),
                    'type' => 'radio',
                    'default' => 'disabled',
                    'options' => array(
                        'enabled' => __('Enabled', 'give-multi-currency'),
                        'disabled' => __('Disabled', 'give-multi-currency'),
                    ),
                );
                // Only apears if 'multi_currency_enabled_setting_field' is 'enabled'
                if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                    $new_setting[] = array(
                        'name' => __('Enabled Currencies', 'give-multi-currency'),
                        'id' => 'multi_currency_active_currency',
                        'desc' => __('Select the currencies your form will accept', 'give-multi-currency') . '<br><a href="https://www.linknacional.com.br/wordpress/givewp/multimoeda/#nova-moeda" target="_blank">' . __('Add new currency', 'give-multi-currency') . '</a>',
                        'type' => 'multicheck',
                        'default' => 1,
                        'options' => array(
                            'brl' => __('Brazilian Real (R$)', 'give-multi-currency'),
                            'usd' => __('US Dollar ($)', 'give-multi-currency'),
                            'eur' => __('Euro (€)', 'give-multi-currency'),
                            'jpy' => __('Japanese Yen (¥)', 'give-multi-currency'),
                            'gbp' => __('British Pound (£)', 'give-multi-currency'),
                            'sar' => __('Saudi Riyal (ر.س)', 'give-multi-currency'),
                            'mxn' => __('Mexican Peso ($)', 'give-multi-currency'),
                            'chf' => __('Swiss Franc (CHF)', 'give-multi-currency')
                        ),
                    );
                }

                // Default currency option
                if (give_get_option('multi_currency_enabled_setting_field') == 'enabled' && give_get_option('currency') == 'BRL') {
                    $new_setting[] = array(
                        'name' => __('Default Currency', 'give-multi-currency'),
                        'id' => 'multi_currency_default_currency',
                        'desc' => __('Select the default currency', 'give-multi-currency'),
                        'type' => 'radio',
                        'default' => 'BRL',
                        'options' => array(
                            'BRL' => __('Brazilian Real (R$)', 'give-multi-currency'),
                            'USD' => __('US Dollar ($)', 'give-multi-currency'),
                            'EUR' => __('Euro (€)', 'give-multi-currency'),
                            'JPY' => __('Japanese Yen (¥)', 'give-multi-currency'),
                            'GBP' => __('British Pound (£)', 'give-multi-currency'),
                            'SAR' => __('Saudi Riyal (ر.س)', 'give-multi-currency'),
                            'MXN' => __('Mexican Peso ($)', 'give-multi-currency'),
                            'CHF' => __('Swiss Franc (CHF)', 'give-multi-currency')
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
    <?php esc_html_e('Custom form is not relevant for GiveWP Multi-Step forms. If you want to use the Free Form Plugin, you need to change the form template to "Legacy" option.', 'give-multi-currency'); ?>
</p>
<?php

        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
?>