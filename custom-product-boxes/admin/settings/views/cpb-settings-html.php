<?php
/**
 * CPB Settings HTML
 *
 * @package CPB/Admin/Settings
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

global $cpb_admin_settings;
?>
<form method="post" id="mainform" action="" enctype="multipart/form-data">
<div class="wrap">
	<h2 class = 'cpb-header'><?php esc_html_e( 'General Settings', 'custom-product-boxes' ); ?></h2>
	<table class = 'form-table' id = "cpb-settings-table">
	 <tbody>
		 <tr valign="top" class="grand_total">
		  <th scope="row" class="titledesc">
			  <label for="cpb_grand_total_label">
			   <?php esc_html_e( 'Grand Total Label', 'custom-product-boxes' ); ?>
			  </label>
		  </th>
		  <td class="forminp forminp-text">
			  <?php echo cpb_help_tip( __( 'Label for total price of box.', 'custom-product-boxes' ), true ); // @codingStandardsIgnoreLine. ?>
			  <input 
			   name="cpb_grand_total_label"
			   id="cpb_grand_total_label"
			   type="text"
			   style="min-width:50px;"
			   value="<?php echo esc_attr( $cpb_admin_settings['cpb_grand_total_label'] ); ?>"
			   class=""
			   placeholder="Grand Total"
			  />
		  </td>
		 </tr>
		 <tr valign="top" class="gift_box_total">
		  <th scope="row" class="titledesc"><?php esc_html_e( 'Show Box Total', 'custom-product-boxes' ); ?></th>
		  <td class="forminp forminp-checkbox">
			  <?php echo cpb_help_tip( __( 'Displays the price of the box elements which will be added to the grand total price. If enabled, the label will be shown below the product.', 'custom-product-boxes' ), true ); // @codingStandardsIgnoreLine. ?>
			  <fieldset>
			   <legend class="screen-reader-text">
				<span><?php esc_html_e( 'Show Box Total', 'custom-product-boxes' ); ?></span>
			   </legend>
			   <label for="cpb_enable_giftbox_total">
				<input name="cpb_enable_giftbox_total" id="cpb_enable_giftbox_total" type="checkbox" class="" <?php checked( 'on', $cpb_admin_settings['cpb_enable_giftbox_total'], true ); ?> /> 
				<?php esc_html_e( 'If enabled shows Gift Box Total below the product.', 'custom-product-boxes' ); ?>
			   </label>
			   <p class="description">
			   </p>
			  </fieldset>
		  </td>
		 </tr>
		 <tr valign="top" class="gift_box_total_hide">
		  <th scope="row" class="titledesc">
			  <label for="cpb_giftbox_total_label">
			   <?php esc_html_e( 'Box total label', 'custom-product-boxes' ); ?>
			  </label>
		  </th>
		  <td class="forminp forminp-text">
			  <?php echo cpb_help_tip( __( 'Label for Box total.', 'custom-product-boxes' ), true ); // @codingStandardsIgnoreLine. ?>
			  <input 
			   name="cpb_giftbox_total_label"
			   id="cpb_giftbox_total_label"
			   type="text"
			   style="min-width:50px;"
			   value="<?php echo esc_attr( $cpb_admin_settings['cpb_giftbox_total_label'] ); ?>"
			   class=""
			   placeholder="Box Total"
			  />
		  </td>
		 </tr>
		 <tr valign="top" class="add_box_charges">
		  <th scope="row" class="titledesc"><?php esc_html_e( 'Enable Additional Box Charges Total', 'custom-product-boxes' ); ?></th>
		  <td class="forminp forminp-checkbox">
			  <?php echo cpb_help_tip( __( 'Displays the charges for the box if applicable.  If enabled, the label will be shown below the product.', 'custom-product-boxes' ), true ); // @codingStandardsIgnoreLine. ?>
			  <fieldset>
			   <legend class="screen-reader-text">
				<span><?php esc_html_e( 'Show Box Charges', 'custom-product-boxes' ); ?></span>
			   </legend>
			   <label for="cpb_enable_addbox_total">
				<input name="cpb_enable_addbox_total" id="cpb_enable_addbox_total" type="checkbox" class="" <?php checked( 'on', $cpb_admin_settings['cpb_enable_addbox_total'], true ); ?> /> 
				<?php esc_html_e( 'If enabled shows Additional Box Charges Total below the product.', 'custom-product-boxes' ); ?>
			   </label>
			   <p class="description">
			   </p>
			  </fieldset>
		  </td>
		 </tr>
		 <tr valign="top" class="add_box_charges_hide">
		  <th scope="row" class="titledesc">
			  <label for="cpb_addbox_total_label">
			   <?php esc_html_e( 'Addition Box Charges Total Label', 'custom-product-boxes' ); ?>
			  </label>
		  </th>
		  <td class="forminp forminp-text">
			  <?php echo cpb_help_tip( __( 'Label for box charges if applicable.', 'custom-product-boxes' ), true ); // @codingStandardsIgnoreLine. ?>
			  <input 
			   name="cpb_addbox_total_label"
			   id="cpb_addbox_total_label"
			   type="text"
			   style="min-width:50px;"
			   value="<?php echo esc_attr( $cpb_admin_settings['cpb_addbox_total_label'] ); ?>"
			   class=""
			   placeholder="Additional Box Charges"
			  />
		  </td>
		 </tr>
		 <tr valign="top" class="cpb_hide_stock_msg">
		  <th scope="row" class="titledesc"><?php esc_html_e( 'Hide stock message', 'custom-product-boxes' ); ?></th>
		  <td class="forminp forminp-checkbox">
			  <?php echo cpb_help_tip( __( 'Hides the stock message from the add-on products', 'custom-product-boxes' ), true ); // @codingStandardsIgnoreLine. ?>
			  <fieldset>
			   <legend class="screen-reader-text">
				<span><?php esc_html_e( 'Hide stock message', 'custom-product-boxes' ); ?></span>
			   </legend>
			   <label for="cpb_hide_stock">
				<input name="cpb_hide_stock" id="cpb_hide_stock" type="checkbox" class="" <?php checked( 'on', $cpb_admin_settings['cpb_hide_stock'], true ); ?> /> 
				<?php esc_html_e( 'Hides the stock message from the add-on products', 'custom-product-boxes' ); ?>
			   </label>
			   <p class="description">
			   </p>
			  </fieldset>
		  </td>
		 </tr>
	 </tbody>
	</table>
	<?php do_action( 'cpb_show_privacy_settings' ); ?>
	<p class="submit">
	 <button name="save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">
	 <?php esc_html_e( 'Save changes', 'custom-product-boxes' ); ?>
	 </button>
	 <?php
		wp_nonce_field( 'cpb_settings_action', 'cpb_settings_field' );
		?>
	</p>
</div>
</form>
