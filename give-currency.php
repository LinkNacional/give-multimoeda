<?php
/**
 * Plugin Name: Give - Multi-Moedas
 * Plugin URI:  https://www.linknacional.com.br/wordpress/givewp/multimoeda/
 * Description: Adiciona opções de escolha de moedas aos formulários do GiveWP.
 * Version:     3.0.2
 * Author:      Link Nacional
 * Requires Plugins: give
 * Author URI:  https://www.linknacional.com.br
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly. ABSPATH is attribute in wp-admin - plugin.php
if ( ! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/Includes/plugin-updater/plugin-update-checker.php';
require_once __DIR__ . '/vendor/autoload.php';

use Lkn\GiveMultimoedas\Includes\GiveMultiCurrency;

if ( ! defined('GIVE_MULTI_CURRENCY_VERSION')) {
    define('GIVE_MULTI_CURRENCY_VERSION', '3.0.2');
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

