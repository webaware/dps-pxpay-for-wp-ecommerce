<?php
// custom fields for WP eCommerce admin page

if (!defined('ABSPATH')) {
	exit;
}

?>

	<tr>
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_userID"><?php echo esc_html_x('User ID', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<input type="text" class="regular-text" name="dps_pxpay_wp_ecommerce[userID]" id="dps_pxpay_wp_ecommerce_userID" value="<?php echo esc_attr($options['userID']); ?>" />
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_userKey"><?php echo esc_html_x('User Key', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<input type="text" class="large-text" name="dps_pxpay_wp_ecommerce[userKey]" id="dps_pxpay_wp_ecommerce_userKey" value="<?php echo esc_attr($options['userKey']); ?>" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<?php echo esc_html_x('Use Sandbox (testing)', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?>
		</th>
		<td>
			<fieldset>
				<input type="radio" name="dps_pxpay_wp_ecommerce[useTest]" id="dps_pxpay_wp_ecommerce_useTest_yes" value="1" <?php checked($options['useTest'], '1'); ?> />&nbsp;<label for="dps_pxpay_wp_ecommerce_useTest_yes"><?php echo TXT_WPSC_YES; ?></label>
				&nbsp;&nbsp;<input type="radio" name="dps_pxpay_wp_ecommerce[useTest]" id="dps_pxpay_wp_ecommerce_useTest_no" value="0" <?php checked($options['useTest'], '0'); ?> />&nbsp;<label for="dps_pxpay_wp_ecommerce_useTest_no"><?php echo TXT_WPSC_NO; ?></label>
				<p class="dpspxpay-wpsc-opt-admin-test"><?php echo esc_html_x('Sandbox requires a separate account that has not been activated for live payments.', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></p>
			</fieldset>

			<fieldset class="dpspxpay-wpsc-opt-admin-test" id="dps_pxpay_wp_ecommerce_testEnv">
				<legend>
					<?php echo esc_html_x('Please select which environment', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?>
				</legend>
				<input type="radio" name="dps_pxpay_wp_ecommerce[testEnv]" id="dps_pxpay_wp_ecommerce_testEnv_sec" value="SEC" <?php checked($options['testEnv'], 'SEC'); ?> />&nbsp;<label for="dps_pxpay_wp_ecommerce_testEnv_sec">SEC</label>
				&nbsp;&nbsp;<input type="radio" name="dps_pxpay_wp_ecommerce[testEnv]" id="dps_pxpay_wp_ecommerce_testEnv_uat" value="UAT" <?php checked($options['testEnv'], 'UAT'); ?> />&nbsp;<label for="dps_pxpay_wp_ecommerce_testEnv_uat">UAT</label>
				<p class="description"><?php echo esc_html_x('When DPS Payment Express sent you your user ID and password, they will have told you to use either SEC or UAT for your sandbox.', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></p>
			</fieldset>
		</td>
	</tr>

	<tr class="dpspxpay-wpsc-opt-admin-test">
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_testID"><?php echo esc_html_x('Test ID', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<input type="text" class="regular-text" name="dps_pxpay_wp_ecommerce[testID]" id="dps_pxpay_wp_ecommerce_testID" value="<?php echo esc_attr($options['testID']); ?>" />
		</td>
	</tr>

	<tr class="dpspxpay-wpsc-opt-admin-test">
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_testKey"><?php echo esc_html_x('Test Key', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<input type="text" class="large-text" name="dps_pxpay_wp_ecommerce[testKey]" id="dps_pxpay_wp_ecommerce_testKey" value="<?php echo esc_attr($options['testKey']); ?>" />
		</td>
	</tr>

	<tr valign="top">
		<th scope="row" id="dps_pxpay_wp_ecommerce_logging_label"><?php echo esc_html_x('Logging', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></th>
		<td>
			<input type="radio" value="off" name="dps_pxpay_wp_ecommerce[logging]" id="dps_pxpay_wp_ecommerce_logging_off" <?php checked($options['logging'], 'off'); ?> aria-labelledby="dps_pxpay_wp_ecommerce_logging_label dps_pxpay_wp_ecommerce_logging_label_off" />
			<label for="dps_pxpay_wp_ecommerce_logging_off" id="dps_pxpay_wp_ecommerce_logging__label_off"><?php echo esc_html_x('Off', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></label> &nbsp;
			<input type="radio" value="info" name="dps_pxpay_wp_ecommerce[logging]" id="dps_pxpay_wp_ecommerce_logging_info" <?php checked($options['logging'], 'info'); ?> aria-labelledby="dps_pxpay_wp_ecommerce_logging_label dps_pxpay_wp_ecommerce_logging_label_info" />
			<label for="dps_pxpay_wp_ecommerce_logging_info" id="dps_pxpay_wp_ecommerce_logging_label_info"><?php echo esc_html_x('All messages', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></label> &nbsp;
			<input type="radio" value="error" name="dps_pxpay_wp_ecommerce[logging]" id="dps_pxpay_wp_ecommerce_logging_error" <?php checked($options['logging'], 'error'); ?> aria-labelledby="dps_pxpay_wp_ecommerce_logging_label dps_pxpay_wp_ecommerce_logging_label_error" />
			<label for="dps_pxpay_wp_ecommerce_logging_error" id="dps_pxpay_wp_ecommerce_logging_label_error"><?php echo esc_html_x('Errors only', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?></label> &nbsp;
			<p class="description"><?php echo esc_html_x('enable logging to assist trouble shooting', 'gateway settings', 'dps-pxpay-for-wp-ecommerce'); ?>
			<br />the log file can be found in <br /><?php echo esc_html(substr(DpsPxPayWpscLogging::getLogFolder(), strlen(ABSPATH))); ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_merchant_ref"><?php echo esc_html_x('Merchant Reference', 'mapped field', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<select name="dps_pxpay_wp_ecommerce[merchant_ref]" id="dps_pxpay_wp_ecommerce_merchant_ref">
				<?php self::showCheckoutFormFields($options['merchant_ref'], __('Order ID', 'gateway settings', 'dps-pxpay-for-wp-ecommerce')); ?>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_txndata1"><?php echo esc_html_x('TxnData1', 'mapped field', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<select name="dps_pxpay_wp_ecommerce[txndata1]" id="dps_pxpay_wp_ecommerce_txndata1">
				<?php self::showCheckoutFormFields($options['txndata1']); ?>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_txndata2"><?php echo esc_html_x('TxnData2', 'mapped field', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<select name="dps_pxpay_wp_ecommerce[txndata2]" id="dps_pxpay_wp_ecommerce_txndata2">
				<?php self::showCheckoutFormFields($options['txndata2']); ?>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_txndata3"><?php echo esc_html_x('TxnData3', 'mapped field', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<select name="dps_pxpay_wp_ecommerce[txndata3]" id="dps_pxpay_wp_ecommerce_txndata3">
				<?php self::showCheckoutFormFields($options['txndata3']); ?>
			</select>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row">
			<label for="dps_pxpay_wp_ecommerce_email"><?php echo esc_html_x('Email Address', 'mapped field', 'dps-pxpay-for-wp-ecommerce'); ?></label>
		</th>
		<td>
			<select name="dps_pxpay_wp_ecommerce[email]" id="dps_pxpay_wp_ecommerce_email">
				<?php self::showCheckoutFormFields($options['email']); ?>
			</select>
		</td>
	</tr>

