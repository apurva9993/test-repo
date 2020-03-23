<?php
/**
 * Admin View: Custom Notices
 *
 * @package CPB/View
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated woocommerce-message">
	<a class="woocommerce-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'cpb-hide-notice', $notice ), 'cpb_hide_notices_nonce', '_cpb_notice_nonce' ) ); ?>"><?php esc_html_e( 'Dismiss', 'custom-product-boxes' ); ?></a>
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>
