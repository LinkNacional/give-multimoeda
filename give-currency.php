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

require_once __DIR__ . '/plugin-updater/plugin-update-checker.php';
require_once __DIR__ . "/vendor/autoload.php";
// Exit if accessed directly. ABSPATH is attribute in wp-admin - plugin.php
if ( ! defined('ABSPATH')) {
    exit;
}

function Give_Multi_Currency() {
    return new GiveMultiCurrency();
}

Give_Multi_Currency();

/**
 * Instance of update checker
 *
 * @return object
 */
function lkn_give_multi_currency_updater() {
    return new Lkn_Puc_Plugin_UpdateChecker(
        'https://api.linknacional.com.br/v2/u/?slug=give-multimoeda',
        __FILE__,//(caso o plugin não precise de compatibilidade com ioncube utilize: __FILE__), //Full path to the main plugin file or functions.php.
        'give-multimoeda'
    );
}

lkn_give_multi_currency_updater();

if ( ! function_exists('get_plugins') || ! function_exists('is_plugin_active')) {
    require_once ABSPATH . '/wp-admin/includes/plugin.php';
}
if (is_plugin_inactive('give-cielo/lkn-give-cielo.php')) {
    $lkn_multicurrency_all_plugins = get_plugins();

    if (isset($lkn_multicurrency_all_plugins['give-cielo/lkn-give-cielo.php']) && ! isset($lkn_multicurrency_all_plugins['give-cielo/give-cielo.php'])) {
        add_action('admin_notices', '__lkn_multicurrency_linkn_inactive_notice');
    }
}

/**
 * Notice for IonCube not found.
 *
 * @since 4.0.1
 */
function __lkn_multicurrency_linkn_inactive_notice(): void {
    $message = '<div id="message" class="error"><p><b>Atenção: </b>O plugin Give Multimoedas detectou que o plugin Cielo API 3.0 encontra-se inativo. <a href="plugins.php">Ativar na área de plugins</a>.</div>';

    echo $message;
}