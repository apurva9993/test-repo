<?php
/**
 * Display notices in admin
 *
 * @package CPB\Admin
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * CPB_Admin_Notices Class.
 */
class CPB_Admin_Notices {

	/**
	 * Stores notices.
	 *
	 * @var array
	 */
	private static $cpb_notices = array();

	/**
	 * Array of notices - name => callback.
	 *
	 * @var array
	 */
	private static $core_cpb_notices = array(
		'cpb-update'                       => 'cpb_update_notice',
	);

	/**
	 * Show a notice.
	 *
	 * @param string $name Notice name.
	 */
	public static function add_notice( $name ) {
		self::$cpb_notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );
	}


	/**
	 * Init function.
	 */
	public static function init() {
		self::$cpb_notices = get_option( 'cpb_admin_notices', array() );

		add_action( 'wp_loaded', array( __CLASS__, 'cpb_hide_notices' ) );
		add_action( 'shutdown', array( __CLASS__, 'store_notices' ) );

		if ( current_user_can( 'manage_woocommerce' ) ) {
			add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ), 0 );
		}
	}

	/**
	 * Store notices to DB
	 */
	public static function store_notices() {
		update_option( 'cpb_admin_notices', self::get_notices() );
	}

	/**
	 * Get notices
	 *
	 * @return array
	 */
	public static function get_notices() {
		return self::$cpb_notices;
	}

	/**
	 * Remove all notices.
	 */
	public static function remove_all_notices() {
		self::$cpb_notices = array();
	}

	/**
	 * Add notices + styles if needed.
	 */
	public static function add_notices() {
		$cpb_notices = self::get_notices();

		if ( empty( $cpb_notices ) ) {
			return;
		}

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins',
		);

		// Notices should only show on WooCommerce screens, the main dashboard, and on the plugins screen.
		if ( cpb_get_screen_id( 'cpb_settings' ) !== $screen_id && ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		foreach ( $cpb_notices as $notice ) {
			if ( ! empty( self::$core_cpb_notices[ $notice ] ) && apply_filters( 'cpb_show_admin_notice', true, $notice ) ) {
				add_action( 'admin_notices', array( __CLASS__, self::$core_cpb_notices[ $notice ] ) );
			} else {
				add_action( 'admin_notices', array( __CLASS__, 'cpb_output_custom_notices' ) );
			}
		}
	}

	/**
	 * Output any stored custom notices.
	 */
	public static function cpb_output_custom_notices() {
		$cpb_notices = self::get_notices();

		if ( ! empty( $cpb_notices ) ) {
			foreach ( $cpb_notices as $notice ) {
				if ( empty( self::$core_cpb_notices[ $notice ] ) ) {
					$notice_html = get_option( 'cpb_admin_notice_' . $notice );

					if ( $notice_html ) {
						include dirname( __FILE__ ) . '/views/cpb-html-notice-custom.php';
					}
				}
			}
		}
	}


	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function cpb_hide_notices() {
		if ( isset( $_GET['cpb-hide-notice'] ) && isset( $_GET['_cpb_notice_nonce'] ) ) { // WPCS: input var ok, CSRF ok.
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_cpb_notice_nonce'] ) ), 'cpb_hide_notices_nonce' ) ) { // WPCS: input var ok, CSRF ok.
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'custom-product-boxes' ) );
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'custom-product-boxes' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['cpb-hide-notice'] ) ); // WPCS: input var ok, CSRF ok.

			self::remove_notice( $hide_notice );

			update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', true );

			do_action( 'cpb_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * If we need to update, include a message with the update button.
	 */
	public static function cpb_update_notice() {
		if ( CPB_Install::needs_db_update() ) {
			$next_scheduled_date = WC()->queue()->get_next( 'cpb_run_update_callback', null, 'cpb-db-updates' );

			if ( $next_scheduled_date || ! empty( $_GET['do_update_cpb'] ) ) { // WPCS: input var ok, CSRF ok.
				include dirname( __FILE__ ) . '/views/cpb-html-notice-updating.php';
			} else {
				include dirname( __FILE__ ) . '/views/cpb-html-notice-update.php';
			}
		} else {
			CPB_Install::update_db_version();
			include dirname( __FILE__ ) . '/views/cpb-html-notice-updated.php';
		}
	}
}

CPB_Admin_Notices::init();
