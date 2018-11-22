<?php
// NB: Minimum PHP version for this file is 5.2! No short array notation, no namespaces!

if (!defined('ABSPATH')) {
	exit;
}

/**
* maybe show notice of minimum PHP version failure
*/
function dps_pxpay_wpsc_fail_php_version() {
	if (dps_pxpay_wpsc_can_show_admin_notices()) {
		dps_pxpay_wpsc_load_text_domain();
		include DPS_PXPAY_WPSC_PLUGIN_ROOT . 'views/requires-php.php';
	}
}

/**
* test whether we can show admin-related notices
* @return bool
*/
function dps_pxpay_wpsc_can_show_admin_notices() {
	global $pagenow, $hook_suffix;

	// only on specific pages
	if ($pagenow !== 'plugins.php' && $hook_suffix !== 'settings_page_wpsc-settings') {
		return false;
	}

	// only bother admins / plugin installers / option setters with this stuff
	if (!current_user_can('activate_plugins') && !current_user_can('manage_options')) {
		return false;
	}

	return true;
}

/**
* load text translations
*/
function dps_pxpay_wpsc_load_text_domain() {
	load_plugin_textdomain('dps-pxpay-for-wp-ecommerce');
}

/**
* replace link placeholders with an external link
* @param string $template
* @param string $url
* @return string
*/
function dps_pxpay_wpsc_external_link($template, $url) {
	$search = array(
		'{{a}}',
		'{{/a}}',
	);
	$replace = array(
		sprintf('<a rel="noopener" target="_blank" href="%s">', esc_url($url)),
		'</a>',
	);
	return str_replace($search, $replace, $template);
}
