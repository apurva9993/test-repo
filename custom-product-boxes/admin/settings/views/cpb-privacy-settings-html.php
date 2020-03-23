<?php
/**
 * CPB Privacy Settings HTML
 *
 * @package CPB/Admin/Settings
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! isset( $msg_deletion_note ) ) {
	$msg_deletion_note = '';
}
if ( ! isset( $label_string ) ) {
	$label_string = '';
}
?>
<h2><?php esc_html_e( 'Privacy Settings', 'custom-product-boxes' ); ?></h2>
<table class = 'form-table' id = "wdm_privacy">
	<tbody>
		<tr valign="top" class="gift_msg_anonymize">
			<th scope="row" class="titledesc"><?php esc_html_e( 'Use gift message feild as personal data?', 'custom-product-boxes' ); ?></th>
			<td class="forminp forminp-checkbox">
				<?php echo \cpb_help_tip( __( "Removes the gift message when woocommerce wants to remove user's personal data", 'custom-product-boxes' ), true ); ?>
				<fieldset>
					<legend class="screen-reader-text">
						<span><?php esc_html_e( 'Use gift message feild as personal data?', 'custom-product-boxes' ); ?></span>
					</legend>
					<label for="cpb_anonymize_msg">
						<input <?php echo $disable_setting; ?> name="cpb_anonymize_msg" id="cpb_anonymize_msg" type="checkbox" class="" <?php checked( 'on', get_option( 'cpb_anonymize_msg' ), true ); ?> /> 
						<p class = "wdm-notice-p">
						<?php
							echo $setting_msg;
						?>
						</p>
					</label>
					<p class="description">
					</p>
				</fieldset>
			</td>
		</tr>
		<tr valign="top" class ="<?php echo $tr_classes; ?>" style = "<?php echo $hide_labels; ?>">
			<th scope="row" class="titledesc">
				<label for="cpb_old_order_labels">
					<?php esc_html_e( 'Message label(s) of old orders.', 'custom-product-boxes' ); ?>
				</label>
			</th>
			<td class="forminp forminp-text">
				<?php echo \cpb_help_tip( __( 'Used to remove gift message from old orders.', 'custom-product-boxes' ), true ); ?>
				<textarea
					name="cpb_old_order_labels"
					id="cpb_old_order_labels"
					value=""
					class=""
					placeholder="Add message labels"
				><?php echo trim( $label_string ); ?></textarea>
				<p class="description"><?php echo $msg_deletion_note; ?></p>
			</td>
		</tr>
	</tbody>
</table>
