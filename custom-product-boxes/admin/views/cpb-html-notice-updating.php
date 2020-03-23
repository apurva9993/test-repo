<?php
/**
 * Admin View: Notice - Updating
 *
 * @package WooCommerce\Admin
 * @version  4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


$pending_actions_url = admin_url( 'admin.php?page=wc-status&tab=action-scheduler&s=cpb_run_&status=pending' );
$cron_disabled       = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
$cron_cta            = $cron_disabled ? __( 'You can manually run queued updates here.', 'custom-product-boxes' ) : __( 'View progress &rarr;', 'custom-product-boxes' );
?>
<div id="message" class="updated woocommerce-message wc-connect">
	<p>
		<strong><?php esc_html_e( 'Custom Product Boxes database update', 'custom-product-boxes' ); ?></strong><br>
		<?php esc_html_e( 'Custom Product Boxes is updating the database in the background. The database update process may take a little while, so please be patient.', 'custom-product-boxes' ); ?>
		<?php
		if ( $cron_disabled ) {
			echo '<br>' . esc_html__( 'Note: WP CRON has been disabled on your install which may prevent this update from completing.', 'custom-product-boxes' );
		}
		?>
		&nbsp;<a href="<?php echo esc_url( $pending_actions_url ); ?>"><?php echo esc_html( $cron_cta ); ?></a>
	</p>
</div>
