<?php

/**
 * Uninstall Give_Multi_Currency
 *
 * @package     Give
 * @subpackage  Uninstall
 * @copyright   Copyright (c) 2016, GiveWP
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly.
if ( ! defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Picks array containing all give settings
$lknaci_mcfg_settings = give_get_settings();
// Search the array for the keys corresponding to the plugin settings
// And save their name
$lknaci_mcfg_options = array_filter($lknaci_mcfg_settings, function ($key) {
    return strpos($key, 'multi_currency_') === 0;
}, ARRAY_FILTER_USE_KEY);

// Checks if keys exist
if (count($lknaci_mcfg_options) > 0) {
    // If there are, scan the array selecting each option
    foreach (array_keys($lknaci_mcfg_options) as $lknaci_mcfg_option) {
        // Uses the value, which is the name of the key, to delete the give option
        give_delete_option($lknaci_mcfg_option);
    }
}
