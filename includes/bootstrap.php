<?php
if (!defined('ABSPATH')) {
	exit;
}

/**
* kick start the plugin
* NB: WP eCommerce loads at priority 8 so we need to be it if we want to hook it
*/
add_action('plugins_loaded', function() {
	require DPS_PXPAY_WPSC_PLUGIN_ROOT . 'includes/class.DpsPxPayWpscPlugin.php';
	DpsPxPayWpscPlugin::getInstance();
}, 5);
