<?php
/**
 * This class creates setting page for CPB
 *
 * @package CPB/Menu
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class for CPB Settings page.
 */
class CPB_Settings {

	public static $cpb_settings = null;
	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'cpb_menu_page' ), 10 );
	}

	/**
	 * Adds CPB settings menu page.
	 *
	 * @return void
	 */
	public function cpb_menu_page() {
		add_menu_page( 'CPB Administration', 'CPB Settings', 'manage_options', 'cpb_settings', array( $this, 'process_cpb_settings' ) );
	}

	/**
	 * Process CPB settings.
	 *
	 * @return void
	 */
	public function process_cpb_settings() {
		if ( isset( $_POST['cpb_settings_field'] ) && wp_verify_nonce( wp_unslash( $_POST['cpb_settings_field'] ), 'cpb_settings_action' ) ) { // WPCS: input var ok, sanitization ok.
			$this->save_settings();
		}

		add_action( 'cpb_show_privacy_settings', array( $this, 'show_privacy_settings' ), 10 );

		$this->show_settings();
	}

	public static function cpb_get_settings() {
		if ( is_null( self::$cpb_settings ) ) {
			self::$cpb_settings = get_option( 'cpb_admin_settings' );
		}
		return self::$cpb_settings;
	}

	/**
	 * Shows the various tabs in CPB.
	 *
	 * @param string $current current tab name.
	 * @return void
	 */
	public function cpb_administartion_tabs( $current = 'general' ) {
		$tabs = array(
			'general' => __( 'General', 'custom-product-boxes' ),
			'other_extensions' => __( 'Other Extensions', 'custom-product-boxes' ),
		);
		?>
		<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			echo "<a class='nav-tab$class' href='admin.php?page=cpb_settings&tab=$tab'>" . $name . '</a>';  // @codingStandardsIgnoreLine.
		}
		?>
		</h2>
		<?php
	}

	/**
	 * Updates notice dismissal flag.
	 */
	public function update_settings_notice_dismissal_flag() {
		update_option( 'wdmcpb_settings_notice_dismissed', 1 );
		die();
	}

	/**
	 * Gets label string for CPB orders.
	 *
	 * @return String list of comma separated labels.
	 */
	public function get_label_string() {
		global $cpb_settings;
		$existing_labels = get_existing_msg_labels();

		if ( ! empty( $existing_labels ) ) {
			$cpb_settings['cpb_old_order_labels'] = $existing_labels;
			update_option( 'cpb_admin_settings', $cpb_settings );
		}

		return implode( ', ', $existing_labels );
	}

	/**
	 * Show the admin notices for settings page.
	 */
	public function new_settings_page_notice() {
		if ( isset( $_GET['page'] ) && 'cpb_settings' == $_GET['page'] ) {
			return;
		}

		$settings_url = menu_page_url( 'cpb_settings', false );
		$here_text = '<a href = "' . $settings_url . '">' . __( 'here', 'custom-product-boxes' ) . '</a>';
		$plugin_name = '<strong>' . __( 'Custom Product Boxes', 'custom-product-boxes' ) . '</strong>';
		$dismissal_flag = get_option( 'wdmcpb_settings_notice_dismissed', 0 );
		if ( 1 != $dismissal_flag ) {
			?>
			<div class="notice notice-warning wdmcpb-settings-notice is-dismissible" >
				<p><?php echo sprintf( __( '%1$s plugin has added a new settings page, which contains privacy settings. Please check the page %2$s.', 'custom-product-boxes' ), $plugin_name, $here_text );  // @codingStandardsIgnoreLine. ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Display GDPR privacy settings section.
	 *
	 * @return void
	 */
	public function show_privacy_settings() {
		$disable_setting = '';
		$setting_msg = '';
		$hide_labels = '';
		$tr_classes = 'cpb_labels';
		$msg_deletion_note = '';
		$label_string = '';

		if ( is_callable( 'WC' ) && version_compare( WC_VERSION, '3.4', '<' ) ) {
			$disable_setting = 'disabled';
			$setting_msg = __( 'Setting unavailable because woocommerce version is lower then 3.4', 'custom-product-boxes' );
			$hide_labels = 'display:none;';
		}

		$order_number = get_last_order_number();

		if ( ! is_cpb_fresh_install() && has_wc_orders() && $order_number ) {
			$label_string = $this->get_label_string();

			$msg_deletion_note = sprintf( __( 'Gift box messages of orders above order #%1$s will be automatically detected to work seamlessly with new export tool and removal personal data tool of WordPress. BUT you will have to help the system for orders below #%2$s to make them work with export tool and personal data tool. Kindly, add gift message labels used for those orders. Multiple labels can be added by using comma(,) as a separator.', 'custom-product-boxes' ), $order_number, $order_number );  // @codingStandardsIgnoreLine.
		} else {
			$tr_classes .= ' cpb_labels_hide';
			$hide_labels = 'display:none;';
		}

		$args = array(
			'tr_classes'      => $tr_classes,
			'setting_msg'     => $setting_msg,
			'hide_labels'     => $hide_labels,
			'label_string'    => $label_string,
			'disable_setting' => $disable_setting,
		);

		// used in included file.
		$args = $args;   // @codingStandardsIgnoreLine.

		// while pushing to git unused, but used in included file.
		unset( $msg_deletion_note );
		unset( $label_string );

		include dirname( __FILE__ ) . '/views/cpb-privacy-settings-html.php';
	}

	/**
	 * Returns the current tab.
	 *
	 * @return string $current_tab current tab.
	 */
	public function get_current_tab() {

		global $pagenow;
		static $current_tab = null;

		if ( null !== $current_tab ) {
			return $current_tab;
		}

		if ( 'admin.php' == $pagenow && isset( $_GET['page'] ) && 'cpb_settings' == $_GET['page'] ) {
			if ( isset( $_GET['tab'] ) ) { // WPCS: input var ok, sanitization ok.
				$current_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
				return $current_tab;
			}

			$current_tab = 'general';
			return $current_tab;
		}

		$current_tab = false;
		return $current_tab;
	}

	/**
	 * Display CPB settings on CPB menu page.
	 *
	 * @return [type] [description]
	 */
	public function show_settings() {
		global $cpb_admin_settings;
		$current_tab = $this->get_current_tab();

		if ( false === $current_tab ) {
			return;
		}

		?>
		<div class="wrap">
			<?php
				$this->cpb_administartion_tabs( $current_tab );
			?>
			<div id="poststuffIE">
			<?php
			switch ( $current_tab ) {
				case 'general':
					$cpb_admin_settings = get_option( 'cpb_admin_settings' ); // @codingStandardsIgnoreLine.
					include dirname( __FILE__ ) . '/views/cpb-settings-html.php';
					break;
				case 'other_extensions':
					do_action( 'cpb_other_extensions' );
					break;
			}//end of switch.
			?>
			</div>
		</div>
		<?php
	}

	/**
	 * Save CPB settings.
	 *
	 * @return void
	 */
	public function save_settings() {
		if ( ! isset( $_POST['cpb_enable_giftbox_total'] ) ) {
			$_POST['cpb_enable_giftbox_total'] = 'off';
		}
		if ( ! isset( $_POST['cpb_enable_addbox_total'] ) ) {
			$_POST['cpb_enable_addbox_total'] = 'off';
		}
		if ( ! isset( $_POST['cpb_anonymize_msg'] ) ) {
			$_POST['cpb_anonymize_msg'] = 'off';
		}
		if ( ! isset( $_POST['cpb_hide_stock'] ) ) {
			$_POST['cpb_hide_stock'] = 'off';
		}

		$msg_labels = $this->get_label_string();

		$default_data = array(
			'cpb_grand_total_label' => __( 'Grand Total', 'custom-product-boxes' ),
			'cpb_enable_giftbox_total' => 'on',
			'cpb_giftbox_total_label' => __( 'Box Total', 'custom-product-boxes' ),
			'cpb_enable_addbox_total' => 'on',
			'cpb_addbox_total_label' => __( 'Box Charges', 'custom-product-boxes' ),
			'cpb_anonymize_msg' => 'off',
			'cpb_hide_stock' => 'off',
			'cpb_old_order_labels' => $msg_labels,
		);

		$cpb_settings = wp_parse_args( $_POST, $default_data );

		foreach ( $cpb_settings as $key => $value ) {
			if ( 'cpb_old_order_labels' == $key ) {
				$msg_array = array_map( 'trim', explode( ',', $value ) );
				$cpb_settings[ $key ] = $msg_array;
				update_option( 'cpb_old_order_labels', $msg_array );
				continue;
			}
		}
		update_option( 'cpb_admin_settings', $cpb_settings );

		include_once( 'cpb-update-nav.php' );
	}
}
