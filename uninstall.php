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
$lkn_array_mc_options = give_get_settings();
// Search the array for the keys corresponding to the plugin settings
// And save their name
$lkn_array_mc_options = array_filter($lkn_array_mc_options, function ($key) {
    return strpos($key, 'multi_currency_') === 0;
}, ARRAY_FILTER_USE_KEY);
$lkn_array_mc_options = array_keys($lkn_array_mc_options);

// Checks if keys exist
if (count($lkn_array_mc_options) > 0) {
    // If there are, scan the array selecting each option
    for ($c = 0; $c < count($lkn_array_mc_options); $c++) {
        // Uses the value, which is the name of the key, to delete the give option
        give_delete_option($lkn_array_mc_options[$c]);
    }
}
