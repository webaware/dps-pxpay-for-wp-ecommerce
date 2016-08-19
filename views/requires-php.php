
<div class="error">
	<p><?php printf(__('DPS PxPay for WP eCommerce requires PHP %1$s or higher; your website has PHP %2$s which is <a target="_blank" href="%3$s">old, obsolete, and unsupported</a>.', 'dps-pxpay-for-wp-ecommerce'),
			esc_html($php_min), esc_html(PHP_VERSION), 'http://php.net/eol.php'); ?></p>
	<p><?php printf(__('Please upgrade your website hosting. At least PHP %s is recommended.', 'dps-pxpay-for-wp-ecommerce'), '5.6'); ?></p>
</div>
