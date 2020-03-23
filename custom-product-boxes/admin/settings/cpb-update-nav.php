<?php
/**
 * Admin View: Update Nav
 *
 * @package CPB/View
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div
	id="setting-error-settings_updated"
	class="updated settings-error notice is-dismissible"
>
	<p>
		<strong><?php esc_html_e( 'Your settings have been saved.', 'custom-product-boxes' ); ?></strong>
	</p>
	<button type="button" class="notice-dismiss">
		<span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'custom-product-boxes' ); ?></span>
	</button>
</div>
