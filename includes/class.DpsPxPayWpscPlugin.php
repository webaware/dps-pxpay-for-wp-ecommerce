<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
* custom exceptons
*/
class DpsPxPayWpscException extends Exception {}

/**
* plugin controller class
*/
class DpsPxPayWpscPlugin {

	/**
	* static method for getting the instance of this singleton object
	* @return DpsPxPayWpscPlugin
	*/
	public static function getInstance() {
		static $instance = null;

		if (is_null($instance)) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	* initialise plugin
	*/
	private function __construct() {
		add_action('init', 'dps_pxpay_wpsc_load_text_domain');
		add_filter('plugin_row_meta', [$this, 'addPluginDetailsLinks'], 10, 2);
		add_action('admin_notices', [$this, 'checkPrerequisites']);

		// register with WP eCommerce
		add_filter('wpsc_merchants_modules', [$this, 'registerGateway'], 1000);
		add_action('wpsc_init', [__CLASS__, 'registerAutoloader']);
	}

	/**
	* register our autoloader (needs to be done *after* WP eCommerce has loaded)
	*/
	public static function registerAutoloader() {
		spl_autoload_register([__CLASS__, 'autoload']);
	}

	/**
	* check for required PHP extensions, tell admin if any are missing
	*/
	public function checkPrerequisites() {
		if (!dps_pxpay_wpsc_can_show_admin_notices()) {
			return;
		}

		// need these PHP extensions
		$missing = array_filter(['libxml', 'pcre', 'SimpleXML', 'xmlwriter'], function($ext) {
			return !extension_loaded($ext);
		});
		if (!empty($missing)) {
			include DPS_PXPAY_WPSC_PLUGIN_ROOT . 'views/requires-extensions.php';
		}
	}

	/**
	* register new WP eCommerce payment gateway
	* @param array $gateways array of registered gateways
	* @return array
	*/
	public function registerGateway($gateways) {
		require DPS_PXPAY_WPSC_PLUGIN_ROOT . 'includes/class.DpsPxPayWpscGateway.php';
		require DPS_PXPAY_WPSC_PLUGIN_ROOT . 'includes/class.DpsPxPayWpscLogging.php';

		return DpsPxPayWpscGateway::register($gateways);
	}

	/**
	* action hook for adding plugin details links
	*/
	public function addPluginDetailsLinks($links, $file) {
		if ($file === DPS_PXPAY_WPSC_PLUGIN_NAME) {
			$links[] = sprintf('<a href="https://wordpress.org/support/plugin/dps-pxpay-for-wp-ecommerce" rel="noopener" target="_blank">%s</a>', _x('Get help', 'plugin details links', 'dps-pxpay-for-wp-ecommerce'));
			$links[] = sprintf('<a href="https://wordpress.org/plugins/dps-pxpay-for-wp-ecommerce/" rel="noopener" target="_blank">%s</a>', _x('Rating', 'plugin details links', 'dps-pxpay-for-wp-ecommerce'));
			$links[] = sprintf('<a href="https://translate.wordpress.org/projects/wp-plugins/dps-pxpay-for-wp-ecommerce" rel="noopener" target="_blank">%s</a>', _x('Translate', 'plugin details links', 'dps-pxpay-for-wp-ecommerce'));
			$links[] = sprintf('<a href="https://shop.webaware.com.au/donations/?donation_for=DPS+PxPay+for+WP+eCommerce" rel="noopener" target="_blank">%s</a>', _x('Donate', 'plugin details links', 'dps-pxpay-for-wp-ecommerce'));
		}

		return $links;
	}

	/**
	* load template from theme or plugin
	* @param string $template name of template file
	* @param array $variables an array of variables that should be accessible by the template
	*/
	public static function loadTemplate($template, $variables) {
		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

		// make variables available to the template
		extract($variables);

		// can't use locate_template() because WP eCommerce is _doing_it_wrong() again!
		// (STYLESHEETPATH and TEMPLATEPATH are both undefined when this function called for wpsc)

		// check in theme / child theme folder
		$templatePath = get_stylesheet_directory() . "/$template";
		if (!file_exists($templatePath)) {
			// check in parent theme folder
			$templatePath = get_template_directory() . "/$template";
			if (!file_exists($templatePath)) {
				// not found in theme, use plugin's template
				$templatePath = DPS_PXPAY_WPSC_PLUGIN_ROOT . "templates/$template";
			}
		}

		require $templatePath;
	}

	/**
	* autoload classes as/when needed
	* @param string $class_name name of class to attempt to load
	*/
	public static function autoload($class_name) {
		static $classMap = [
			'DpsPxPayWpscPayment'				=> 'includes/class.DpsPxPayWpscPayment.php',
			'DpsPxPayWpscResponse'				=> 'includes/class.DpsPxPayWpscResponse.php',
			'DpsPxPayWpscResponseRequest'		=> 'includes/class.DpsPxPayWpscResponseRequest.php',
			'DpsPxPayWpscResponseResult'		=> 'includes/class.DpsPxPayWpscResponseResult.php',
		];

		if (isset($classMap[$class_name])) {
			require DPS_PXPAY_WPSC_PLUGIN_ROOT . $classMap[$class_name];
		}
	}

}
