<?php
/*
Plugin Name: DPS PxPay for WP eCommerce
Plugin URI: https://wordpress.org/plugins/dps-pxpay-for-wp-ecommerce/
Description: Integrate DPS PxPay with the WP eCommerce online shop
Version: 1.1.1
Author: WebAware
Author URI: https://shop.webaware.com.au/
Text Domain: dps-pxpay-for-wp-ecommerce
*/

/*
copyright (c) 2016-2021 WebAware Pty Ltd (email : support@webaware.com.au)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if (!defined('ABSPATH')) {
	exit;
}

define('DPS_PXPAY_WPSC_PLUGIN_FILE', __FILE__);
define('DPS_PXPAY_WPSC_PLUGIN_ROOT', dirname(__FILE__) . '/');
define('DPS_PXPAY_WPSC_PLUGIN_NAME', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('DPS_PXPAY_WPSC_MIN_PHP', '5.6');
define('DPS_PXPAY_WPSC_VERSION', '1.1.1');

require DPS_PXPAY_WPSC_PLUGIN_ROOT . 'includes/functions-global.php';

if (version_compare(PHP_VERSION, DPS_PXPAY_WPSC_MIN_PHP, '<')) {
	add_action('admin_notices', 'dps_pxpay_wpsc_fail_php_version');
	return;
}

require DPS_PXPAY_WPSC_PLUGIN_ROOT . 'includes/bootstrap.php';
