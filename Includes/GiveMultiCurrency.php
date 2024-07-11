<?php

namespace Lkn\GiveMultimoedas\Includes;

use Lkn\GiveMultimoedas\Admin\GiveMultiCurrencyAdmin;
use Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyActions;
use Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyLoader;
use Phan\Language\Element\Func;

final class GiveMultiCurrency
{
    /**
     * @since
     * @access private
     * @var GiveMultiCurrencyLoader
     */
    private GiveMultiCurrencyLoader $loader;

    /**
     * Give - Multi Currency Admin Object.
     *
     * @since  1.0.0
     * @access public
     *
     * @var    Give_Multi_Currency_Admin object.
     */
    public $plugin_admin;

    /**
     * Give - Multi Currency Frontend Object.
     *
     * @since  1.0.0
     * @access public
     *
     * @var    Give_Multi_Currency_Frontend object.
     */
    public $plugin_public;

    /**
     * Singleton pattern.
     *
     * @since
     * @access private
     */
    public function __construct()
    {
        $this->load_dependency();
        $this->setup();
        $this->setup_hooks();
    }

    /**
     * Setup
     *
     * @since
     * @access private
     */
    private function setup(): void
    {
        $this->setup_constants();
    }

    /**
     * Setup constants
     *
     * Defines useful constants to use throughout the add-on.
     *
     * @since
     * @access private
     */
    private function setup_constants(): void
    {
        // Defines addon version number for easy reference.
        if (! defined('GIVE_MULTI_CURRENCY_VERSION')) {
            define('GIVE_MULTI_CURRENCY_VERSION', '2.7.0');
        }

        // Set it to latest.
        if (! defined('GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION')) {
            define('GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION', '2.19.2');
        }

        if (! defined('GIVE_MULTI_CURRENCY_FILE')) {
            define('GIVE_MULTI_CURRENCY_FILE', __FILE__);
        }

        if (! defined('GIVE_MULTI_CURRENCY_SLUG')) {
            define('GIVE_MULTI_CURRENCY_SLUG', 'give-multi-currency');
        }

        if (! defined('GIVE_MULTI_CURRENCY_DIR')) {
            define('GIVE_MULTI_CURRENCY_DIR', plugin_dir_path(GIVE_MULTI_CURRENCY_FILE));
        }

        if (! defined('GIVE_MULTI_CURRENCY_URL')) {
            define('GIVE_MULTI_CURRENCY_URL', plugin_dir_url(GIVE_MULTI_CURRENCY_FILE));
        }

        if (! defined('GIVE_MULTI_CURRENCY_BASENAME')) {
            define('GIVE_MULTI_CURRENCY_BASENAME', plugin_basename(GIVE_MULTI_CURRENCY_FILE));
        }
    }

    /**
     * Plugin installation
     *
     * @since
     * @access public
     */
    public function install(): void
    {
        // Bailout.
        if (! $this->check_environment()) {
            return;
        }
    }

    /**
     * Check plugin environment
     *
     * @return bool|null
     * @since
     * @access public
     *
     */
    public function check_environment()
    {
        // Is not admin

        // Load plugin helper functions.
        if (! function_exists('deactivate_plugins') || ! function_exists('is_plugin_active')) {
            require_once ABSPATH . '/wp-admin/includes/plugin.php';
        }

        // Flag to check whether deactivate plugin or not.
        $is_deactivate_plugin = false;

        // Verify dependency cases.
        switch (true) {
            case doing_action('give_init'):
                if (
                    defined('GIVE_VERSION') &&
                    version_compare(GIVE_VERSION, GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION, '<')
                ) {
                    /* Min. Give. plugin version. */

                    // Show admin notice.
                    add_action('admin_notices', 'lkn_give_multi_currency_dependency_notice');

                    $is_deactivate_plugin = true;
                }

                break;

            case doing_action('activate_' . GIVE_MULTI_CURRENCY_BASENAME):
            case doing_action('plugins_loaded') && ! did_action('give_init'):
                /* Check to see if Give is activated, if it isn't deactivate and show a banner. */

                // Check for if give plugin activate or not.
                $is_give_active = defined('GIVE_PLUGIN_BASENAME') ? is_plugin_active(GIVE_PLUGIN_BASENAME) : false;

                if (! $is_give_active) {
                    add_action('admin_notices', 'lkn_give_multi_currency_inactive_notice');

                    $is_deactivate_plugin = true;
                }

                break;
        }

        // Don't let this plugin activate.
        if ($is_deactivate_plugin) {
            // Deactivate plugin.
            deactivate_plugins(GIVE_MULTI_CURRENCY_BASENAME);

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }

            return false;
        }

        return true;
    }

    /**
     * Load plugin files.
     *
     * @since
     * @access private
     */
    private function load_dependency(): void
    {
        $this->loader = new GiveMultiCurrencyLoader();
    }

    /**
     * Setup hooks
     *
     * @since
     * @access private
     */
    private function setup_hooks(): void
    {
        $this->admin_hooks();
        $this->public_hooks();
        $this->loader->run();
    }

    private function admin_hooks(): void
    {
        register_activation_hook(GIVE_MULTI_CURRENCY_FILE, array($this, 'install'));
        $plugin_admin = new GiveMultiCurrencyAdmin();
        $this->loader->add_filter('give_get_settings_general', $plugin_admin, 'lkn_give_multi_currency_add_setting_into_existing_tab');
        $this->loader->add_filter('give_metabox_form_data_settings', $plugin_admin, 'setup_setting', 999);
    }

    private function public_hooks(): void
    {
        $this->loader->add_action('plugins_loaded', $this, 'check_environment', 999);
        $this->loader->add_filter('plugin_action_links_' . GIVE_MULTI_CURRENCY_BASENAME, 'Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyHelper', 'lkn_give_multi_currency_plugin_row_meta', 10, 2);
        $this->loader->add_filter('give_currency', 'Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyActions', 'lkn_give_change_multi_currency');
        $this->loader->add_filter('give_get_price_thousand_separator', 'Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyActions', 'lkn_give_multi_currency_thousand_separator');
        $this->loader->add_filter('give_get_price_decimal_separator', 'Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyActions', 'lkn_give_multi_currency_decimal_separator');
        $this->loader->add_filter('give_sanitize_amount_decimals', 'Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyActions', 'lkn_give_multi_currency_decimal_count');
        $this->loader->add_action('give_before_donation_levels', 'Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyActions', 'lkn_give_multi_currency_selector', 10, 3);
    }
}
