<?php
// show billing details

if (!defined('ABSPATH')) {
	exit;
}
?>

<blockquote>
	<?php if (!empty($purchlogitem->extrainfo->transactid)): ?>
	<strong>Transaction ID:</strong> <?= esc_html($purchlogitem->extrainfo->transactid); ?><br/>
	<?php endif; ?>
	<?php if (!empty($purchlogitem->extrainfo->authcode)): ?>
	<strong>Auth Code:</strong> <?= esc_html($purchlogitem->extrainfo->authcode); ?><br/>
	<?php endif; ?>
</blockquote>

