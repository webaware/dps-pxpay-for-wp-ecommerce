<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="notice notice-error">
<p>
		<?php echo dps_pxpay_wpsc_external_link(
				sprintf(esc_html__('DPS PxPay for WP eCommerce requires PHP %1$s or higher; your website has PHP %2$s which is {{a}}old, obsolete, and unsupported{{/a}}.', 'dps-pxpay-for-wp-ecommerce'),
					esc_html(DPS_PXPAY_WPSC_MIN_PHP), esc_html(PHP_VERSION)),
				'https://secure.php.net/supported-versions.php'
			); ?>
	</p>
	<p><?php printf(esc_html__('Please upgrade your website hosting. At least PHP %s is recommended.', 'dps-pxpay-for-wp-ecommerce'), '7.4'); ?></p>
</div>
