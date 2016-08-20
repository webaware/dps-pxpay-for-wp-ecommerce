<?php
/*
Plugin Name: DPS PxPay for WP eCommerce
Plugin URI:
Description: Integrate DPS PxPay with the WP eCommerce online shop
Version: 1.0.0
Author: WebAware
Author URI: http://webaware.com.au/
Text Domain: dps-pxpay-for-wp-ecommerce
*/

/*
copyright (c) 2016 WebAware Pty Ltd (email : support@webaware.com.au)

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
define('DPS_PXPAY_WPSC_VERSION', '1.0.0');

// initialise plugin
require DPS_PXPAY_WPSC_PLUGIN_ROOT . 'includes/class.DpsPxPayWpscPlugin.php';
DpsPxPayWpscPlugin::getInstance();
