<?php
/**
 * Admin View: Notice - Update
 *
 * @package WooCommerce\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$update_url = wp_nonce_url(
	add_query_arg( 'do_update_cpb', 'true', admin_url( 'admin.php?page=cpb_settings' ) ),
	'cpb_db_update',
	'cpb_db_update_nonce'
);

?>
<div id ="message" class="updated woocommerce-message wc-connect">
	<p>
		<strong><?php esc_html_e( 'Custom Product Boxes has been updated! The database update required', 'custom-product-boxes' ); ?></strong>
	</p>
	<p>
		<?php
			esc_html_e( 'To keep things running smoothly, we have to update your database to the newest version.', 'custom-product-boxes' );

			esc_html__( 'The database update process runs in the background and may take a little while, so please be patient.', 'custom-product-boxes' );
		?>
	</p>
	<p class="submit">
		<a href="<?php echo esc_url( $update_url ); ?>" class="wc-update-now button-primary">
			<?php esc_html_e( 'Update Custom Product Boxes Database', 'custom-product-boxes' ); ?>
		</a>
		<a href="https://docs.woocommerce.com/document/how-to-update-woocommerce/" class="button-secondary">
			<strong><?php esc_html_e( 'Before updating please backup your database.', 'custom-product-boxes' ); ?></strong>
		</a>
	</p>
</div>
