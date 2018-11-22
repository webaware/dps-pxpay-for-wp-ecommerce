<?php
if (!defined('ABSPATH')) {
	exit;
}
?>

<div class="notice notice-error">
	<p><?php esc_html_e('DPS PxPay for WP eCommerce requires these missing PHP extensions. Please contact your website host to have these extensions installed.', 'dps-pxpay-for-wp-ecommerce'); ?></p>
	<ul style="padding-left: 2em">
		<?php foreach ($missing as $ext): ?>
		<li style="list-style-type:disc"><?= esc_html($ext); ?></li>
		<?php endforeach; ?>
	</ul>
</div>
