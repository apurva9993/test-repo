<?php
/**
 * Admin View: Notice - Updated.
 *
 * @package CPB\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated woocommerce-message wc-connect woocommerce-message--success">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'cpb-hide-notice', 'update', remove_query_arg( 'do_update_cpb' ) ), 'cpb_hide_notices_nonce', '_cpb_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'custom-product-boxes' ); ?></a>

	<p><?php esc_html_e( 'Custom Product Boxes database update complete. Thank you for updating to the latest version!', 'woocommerce' ); ?></p>
</div>
