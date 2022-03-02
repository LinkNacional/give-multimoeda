<?php
/**
 * Plugin Name: Give - Multi Moedas
 * Plugin URI:  https://www.linknacional.com.br
 * Description: Adiciona opções de escolher moedas no formulário do give.
 * Version:     2.4.0
 * Author:      Link Nacional
 * Author URI:  https://www.linknacional.com.br
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: give-multi-currency
 */

require_once __DIR__ . '/plugin-updater/plugin-update-checker.php';

// Exit if accessed directly. ABSPATH is attribute in wp-admin - plugin.php
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class Give_Multi_Currency
 */
final class Give_Multi_Currency {
	/**
	 * Instance.
	 *
	 * @since
	 * @access private
	 * @var Give_Multi_Currency
	 */
	private static $instance;

	/**
	 * Give - MULTI MOEDAS Admin Object.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var    Give_Multi_Currency_Admin object.
	 */
	public $plugin_admin;

	/**
	 * Give - MULTI MOEDAS Frontend Object.
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
	private function __construct() {
		self::$instance = $this;
	}

	/**
	 * Get instance.
	 *
	 * @return Give_Multi_Currency
	 * @since
	 * @access public
	 *
	 */
	public static function get_instance() {
		if (!isset(self::$instance) && !(self::$instance instanceof Give_Multi_Currency)) {
			self::$instance = new Give_Multi_Currency();
			self::$instance->setup();
		}

		return self::$instance;
	}

	/**
	 * Setup
	 *
	 * @since
	 * @access private
	 */
	private function setup() {
		self::$instance->setup_constants();

		register_activation_hook(GIVE_MULTI_CURRENCY_FILE, [$this, 'install']);
		add_action('give_init', [$this, 'init'], 10, 1);
		add_action('plugins_loaded', [$this, 'check_environment'], 999);
	}

	/**
	 * Setup constants
	 *
	 * Defines useful constants to use throughout the add-on.
	 *
	 * @since
	 * @access private
	 */
	private function setup_constants() {
		// Defines addon version number for easy reference.
		if (!defined('GIVE_MULTI_CURRENCY_VERSION')) {
			define('GIVE_MULTI_CURRENCY_VERSION', '1.0');
		}

		// Set it to latest.
		if (!defined('GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION')) {
			define('GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION', '2.3.0');
		}

		if (!defined('GIVE_MULTI_CURRENCY_FILE')) {
			define('GIVE_MULTI_CURRENCY_FILE', __FILE__);
		}

		if (!defined('GIVE_MULTI_CURRENCY_SLUG')) {
			define('GIVE_MULTI_CURRENCY_SLUG', 'give-multi-currency');
		}

		if (!defined('GIVE_MULTI_CURRENCY_DIR')) {
			define('GIVE_MULTI_CURRENCY_DIR', plugin_dir_path(GIVE_MULTI_CURRENCY_FILE));
		}

		if (!defined('GIVE_MULTI_CURRENCY_URL')) {
			define('GIVE_MULTI_CURRENCY_URL', plugin_dir_url(GIVE_MULTI_CURRENCY_FILE));
		}

		if (!defined('GIVE_MULTI_CURRENCY_BASENAME')) {
			define('GIVE_MULTI_CURRENCY_BASENAME', plugin_basename(GIVE_MULTI_CURRENCY_FILE));
		}
	}

	/**
	 * Plugin installation
	 *
	 * @since
	 * @access public
	 */
	public function install() {
		// Bailout.
		if (!self::$instance->check_environment()) {
			return;
		}
	}

	/**
	 * Plugin installation
	 *
	 * @param Give $give
	 *
	 * @return void
	 * @since
	 * @access public
	 *
	 */
	public function init($give) {
		if (!self::$instance->check_environment()) {
			//se não esta logado entra daqui
			self::$instance->load_files();
			self::$instance->setup_hooks();
			self::$instance->load_license();

			return;
		}

		self::$instance->load_files();
		self::$instance->setup_hooks();
		self::$instance->load_license();
	}

	/**
	 * Check plugin environment
	 *
	 * @return bool|null
	 * @since
	 * @access public
	 *
	 */
	public function check_environment() {
		// Não é admin inserir aqui
		if (!is_admin() || !current_user_can('activate_plugins')) {
			require_once GIVE_MULTI_CURRENCY_DIR . 'includes/actions.php';
			require_once GIVE_MULTI_CURRENCY_DIR . 'includes/exchange-rates.php';

			return null;
		}

		// Load plugin helper functions.
		if (!function_exists('deactivate_plugins') || !function_exists('is_plugin_active')) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		// Load helper functions.
		require_once GIVE_MULTI_CURRENCY_DIR . 'includes/misc-functions.php';

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
					add_action('admin_notices', '__give_multi_currency_dependency_notice');

					$is_deactivate_plugin = true;
				}

				break;

			case doing_action('activate_' . GIVE_MULTI_CURRENCY_BASENAME):
			case doing_action('plugins_loaded') && !did_action('give_init'):
				/* Check to see if Give is activated, if it isn't deactivate and show a banner. */

				// Check for if give plugin activate or not.
				$is_give_active = defined('GIVE_PLUGIN_BASENAME') ? is_plugin_active(GIVE_PLUGIN_BASENAME) : false;

				if (!$is_give_active) {
					add_action('admin_notices', '__give_multi_currency_inactive_notice');

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
	private function load_files() {
		require_once GIVE_MULTI_CURRENCY_DIR . 'includes/misc-functions.php';

		if (is_admin()) {
			require_once GIVE_MULTI_CURRENCY_DIR . 'includes/admin/setting-admin.php';
			require_once GIVE_MULTI_CURRENCY_DIR . 'includes/admin/form-settings.php';
		}
	}

	/**
	 * Setup hooks
	 *
	 * @since
	 * @access private
	 */
	private function setup_hooks() {
		// Filters
		add_filter('plugin_action_links_' . GIVE_MULTI_CURRENCY_BASENAME, '__give_multi_currency_plugin_row_meta', 10, 2);
	}

	/**
	 * Load license
	 *
	 * @since
	 * @access private
	 */
	private function load_license() {
		new Give_License(
			GIVE_MULTI_CURRENCY_FILE,
			'Give Multi Currency',
			GIVE_MULTI_CURRENCY_VERSION,
			'WordImpress',
			'give_multi_currency_license_key'
		);
	}
}

/**
 * The main function responsible for returning the one true Give_Currency instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $recurring = Give_Multi_Currency(); ?>
 *
 * @return Give_Multi_Currency|bool
 * @since 1.0
 *
 */
function Give_Multi_Currency() {
	return Give_Multi_Currency::get_instance();
}

Give_Multi_Currency();

/**
 * Instância do updateChecker, ela exige os seguintes parâmetros:
 *
 * url do JSON
 * caminho completo do arquivo principal do plugin
 * nome do diretório
 *
 * @return object
 */
function lkn_give_multimoeda_updater() {
	return new Lkn_Puc_Plugin_UpdateChecker(
		'https://api.linknacional.com.br/app/u/link_api_update.php?slug=give-multimoeda',
		__FILE__,//(caso o plugin não precise de compatibilidade com ioncube utilize: __FILE__), //Full path to the main plugin file or functions.php.
		'give-multimoeda'
	);
}

lkn_give_multimoeda_updater();