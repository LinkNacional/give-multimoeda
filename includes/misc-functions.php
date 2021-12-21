<?php
/**
 * Show plugin dependency notice
 *
 * @since
 */
function __give_multi_currency_dependency_notice() {
	// Admin notice.
	$message = sprintf(
		'<strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a>  %5$s %6$s+ %7$s.',
		__('Activation Error:', 'give-multi-currency'),
		__('You must have', 'give-multi-currency'),
		'https://givewp.com',
		__('Give', 'give-multi-currency'),
		__('version', 'give-multi-currency'),
		GIVE_MULTI_CURRENCY_MIN_GIVE_VERSION,
		__('for the Give add-on to activate', 'give-multi-currency')
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
function __give_multi_currency_inactive_notice() {
	// Admin notice.
	$message = sprintf(
		'<div class="notice notice-error"><p><strong>%1$s</strong> %2$s <a href="%3$s" target="_blank">%4$s</a> %5$s.</p></div>',
		__('Activation Error:', 'give-multi-currency'),
		__('You must have', 'give-multi-currency'),
		'https://givewp.com',
		__('Give', 'give-multi-currency'),
		__(' plugin installed and activated for the Give Addon', 'give-multi-currency')
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
function __give_multi_currency_plugin_row_meta($plugin_meta, $plugin_file) {
	$new_meta_links['setting'] = sprintf(
		'<a href="%1$s">%2$s</a>',
		admin_url('edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings'),
		__('Settings', 'give-multi-currency')
	);

	return array_merge($plugin_meta, $new_meta_links);
}

/**
 * Show activation banner
 *
 * @since
 * @return void
*/
function __give_multi_currency_activation() {
	// Initialize activation welcome banner.
	if (class_exists('Give_Multi_Currency')) {
		// Only runs on admin.
		$args = [
			'file' => GIVE_MULTI_CURRENCY_FILE,
			'name' => __('currency', 'give-multi-currency'),
			'version' => GIVE_MULTI_CURRENCY_VERSION,
			'settings_url' => admin_url('edit.php?post_type=give_forms&page=give-settings&tab=general&section=currency-settings'),
			'documentation_url' => 'https://givewp.com/documentation/add-ons/boilerplate/',
			'support_url' => 'https://givewp.com/support/',
			'testing' => false, // Never leave true.
		];

		new Give_Multi_Currency($args);
	}
}
