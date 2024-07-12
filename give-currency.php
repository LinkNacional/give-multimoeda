<?php
/**
 * Plugin Name: Give - Multi-Moedas
 * Plugin URI:  https://www.linknacional.com.br/wordpress/givewp/
 * Description: Adiciona opções de escolha de moedas aos formulários do GiveWP.
 * Version:     2.7.0
 * Author:      Link Nacional
 * Author URI:  https://www.linknacional.com.br
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

use Lkn\GiveMultimoedas\Includes\GiveMultiCurrency;
use Lkn\GiveMultimoedas\Includes\GiveMultiCurrencyHelper;

require_once __DIR__ . '/plugin-updater/plugin-update-checker.php';
require_once __DIR__ . "/vendor/autoload.php";
// Exit if accessed directly. ABSPATH is attribute in wp-admin - plugin.php
if ( ! defined('ABSPATH')) {
    exit;
}
if ( ! defined('GIVE_MULTI_CURRENCY_VERSION')) {
    define('GIVE_MULTI_CURRENCY_VERSION', '2.7.0');
}

// Set it to latest.
if ( ! defined('GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION')) {
    define('GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION', '2.19.2');
}

if ( ! defined('GIVE_MULTI_CURRENCY_FILE')) {
    define('GIVE_MULTI_CURRENCY_FILE', __FILE__);
}

if ( ! defined('GIVE_MULTI_CURRENCY_SLUG')) {
    define('GIVE_MULTI_CURRENCY_SLUG', 'give-multi-currency');
}

if ( ! defined('GIVE_MULTI_CURRENCY_DIR')) {
    define('GIVE_MULTI_CURRENCY_DIR', plugin_dir_path(GIVE_MULTI_CURRENCY_FILE));
}

if ( ! defined('GIVE_MULTI_CURRENCY_URL')) {
    define('GIVE_MULTI_CURRENCY_URL', plugin_dir_url(GIVE_MULTI_CURRENCY_FILE));
}

if ( ! defined('GIVE_MULTI_CURRENCY_BASENAME')) {
    define('GIVE_MULTI_CURRENCY_BASENAME', plugin_basename(GIVE_MULTI_CURRENCY_FILE));
}

function Give_Multi_Currency() {
    return new GiveMultiCurrency();
}
Give_Multi_Currency();

if ( ! function_exists('get_plugins') || ! function_exists('is_plugin_active')) {
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
}
if (is_plugin_inactive('give-cielo/lkn-give-cielo.php')) {
    $lkn_multicurrency_all_plugins = get_plugins();

    if (isset($lkn_multicurrency_all_plugins['give-cielo/lkn-give-cielo.php']) && ! isset($lkn_multicurrency_all_plugins['give-cielo/give-cielo.php'])) {
        add_action('admin_notices', array('GiveMultiCurrencyHelper', '__lkn_multicurrency_linkn_inactive_notice'));
    }
}

