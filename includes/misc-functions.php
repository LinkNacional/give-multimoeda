<?php
/**
 * Show plugin dependency notice
 *
 * @since
 */
function lkn_give_multi_currency_dependency_notice() {
    // Admin notice.
    $message = sprintf(
        '<strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a>  %5$s %6$s+ %7$s.',
        __('Activation Error:', 'give'),
        __('You must have', 'give'),
        'https://givewp.com',
        __('Give', 'give'),
        __('version', 'give'),
        GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION,
        __('for the Give add-on to activate', 'give')
    );

    Give()->notices->register_notice([
        'id' => 'give-activation-error',
        'type' => 'error',
        'description' => $message,
        'show' => true,
    ]);
}

/**
 * Notice for No Core Activation
 *
 * @since
 */
function lkn_give_multi_currency_inactive_notice() {
    // Admin notice.
    $message = sprintf(
        '<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s.</p></div>',
        __('Activation Error:', 'give'),
        __('You must have', 'give'),
        'https://givewp.com',
        __('Give', 'give'),
        __(' plugin installed and activated for the Give Addon', 'give')
    );

    echo $message;
}

/**
 * Plugin row meta links.
 *
 * @since
 *
 * @param array $plugin_meta An array of the plugin's metadata.
 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
 *
 * @return array
*/
function lkn_give_multi_currency_plugin_row_meta($plugin_meta, $plugin_file) {
    $new_meta_links['setting'] = sprintf(
        '<a href="%1$s">%2$s</a>',
        admin_url('edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings'),
        __('Settings', 'give')
    );

    return array_merge($plugin_meta, $new_meta_links);
}

/**
 * Show activation banner
 *
 * @since
 * @return void
*/
function lkn_give_multi_currency_activation() {
    // Initialize activation welcome banner.
    if (class_exists('Give_Multi_Currency')) {
        // Only runs on admin.
        $args = [
            'file' => GIVE_MULTI_CURRENCY_FILE,
            'name' => __('Multi Currency', 'give'),
            'version' => GIVE_MULTI_CURRENCY_VERSION,
            'settings_url' => admin_url('edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings'),
            'documentation_url' => 'https://www.linknacional.com.br/wordpress/givewp/',
            'support_url' => 'https://www.linknacional.com.br/suporte/',
            'testing' => false, // Never leave true.
        ];

        new Give_Multi_Currency($args);
    }
}
