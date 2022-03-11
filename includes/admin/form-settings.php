<?php
// Exit if access directly.
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Add metabox tab to give form data settings.
 *
 * @package     Give
 * @copyright   Copyright (c) 2020, Impress.org
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class Lkn_Give_Multi_Currency_Settings {
    /**
     * Give_Metabox_Setting_Fields constructor.
     */
    public function __construct() {
        $this->id = 'lkn_multi_currency_fields';
        $this->prefix = '_lkn_multi_currency_';
        add_filter('give_metabox_form_data_settings', [$this, 'setup_setting'], 999);
    }

    public function setup_setting($settings) {
        // Custom metabox settings.
        $settings["{$this->id}_tab"] = [
            'id' => "{$this->id}_tab",
            'title' => __('Opções de moedas', 'give-multi-currency'),
            'icon-html' => '<span class="dashicons dashicons-money-alt"></span>',
            'fields' => [
                [
                    'id' => "{$this->id}_status",
                    'name' => __('Opções globais', 'give-multi-currency'),
                    'type' => 'radio_inline',
                    'desc' => __('Habilitar opções globais para o formulário ou desabilitar para usar as opções definidas no formulário.', 'give-multi-currency'),
                    'options' => [
                        'enabled' => __('Habilitado', 'give-multi-currency'),
                        'disabled' => __('Desabilitado', 'give-multi-currency'),
                    ],
                    'default' => 'enabled',
                ],
                [
                    'id' => "{$this->id}_default_currency",
                    'name' => __('Moeda padrão', 'give-multi-currency'),
                    'type' => 'radio',
                    'desc' => __('Selecione a moeda padrão.', 'give-multi-currency'),
                    'options' => [
                        'BRL' => __('Real Brasileiro (R$)', 'give'),
                        'USD' => __('Dólar Americano ($)', 'give'),
                        'EUR' => __('Euro (€)', 'give'),
                        'JPY' => __('Iene (¥)', 'give'),
                        'GBP' => __('Libra esterlina (£)', 'give'),
                    ],
                    'default' => 'BRL',
                ],
                [
                    'id' => "{$this->id}_active_currency",
                    'name' => __('Moedas Habilitadas', 'give-multi-currency'),
                    'type' => 'multicheck',
                    'desc' => __('Selecione as moedas que seu formulário irá aceitar.', 'give-multi-currency'),
                    'default' => '1',
                    'options' => [
                        'USD' => __('Dólar Americano ($)', 'give'),
                        'EUR' => __('Euro (€)', 'give'),
                        'JPY' => __('Iene (¥)', 'give'),
                        'GBP' => __('Libra esterlina (£)', 'give'),
                    ],
                ],
            ],
        ];

        return $settings;
    }

    /**
     * Add a form admin notice
     *
     * @return string $html
     *
     */
    public function disabled_for_non_legacy_templates_html() {
        ob_start(); ?>
			<p class="ffconfs-disabled"><?php _e('O formulário customizado não é relevante para o formulário Multi-Step do giveWP. Caso você deseje utilizar o Free Form Plugin é necessário mudar o Template do formulário para opção "Legado".', 'give-multi-currency-notices'); ?></p>
		<?php

        $html = ob_get_contents();

        return $html;
    }
}
new Lkn_Give_Multi_Currency_Settings();
