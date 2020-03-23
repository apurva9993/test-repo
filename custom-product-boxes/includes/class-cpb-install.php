<?php
/**
 * Installation related functions and actions.
 *
 * @package CPB/Classes
 * @version 4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Install Class.
 */
class CPB_Install {

	/**
	 * DB updates and callbacks that need to be run per version.
	 *
	 * @var array
	 */
	private static $db_updates = array(
		'4.0.0' => array(
			'cpb_update_400_cpb_settings_data',
			'cpb_update_400_wdm_data_to_cpb',
			'cpb_update_400_bundled_data',
		)
	);


	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_cpb_version' ), 5 );
		// add_action( 'init', array( __CLASS__, 'manual_database_update' ), 20 );
		add_action( 'cpb_run_update_callback', array( __CLASS__, 'run_update_callback' ) );
		add_action( 'cpb_run_second_update_callback', array( __CLASS__, 'run_second_update_callback' ), 10, 2 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );

		// add_filter( 'plugin_action_links_' . WC_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		// add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
		// add_filter( 'wpmu_drop_tables', array( __CLASS__, 'wpmu_drop_tables' ) );
		// add_filter( 'cron_schedules', array( __CLASS__, 'cron_schedules' ) );
	}

	/**
	 * Install CPB.
	 */
	public static function install() {
		delete_transient( 'cpb_installing' );
		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'cpb_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'cpb_installing', 'yes', MINUTE_IN_SECONDS * 10 );

		self::create_tables();
		self::migrate_options(); // add more appropriate name
		self::create_options();
		// self::create_terms();
		// For data update of CPB
		self::update_cpb_version();
		self::maybe_update_db_version();

		delete_transient( 'cpb_installing' );

		do_action( 'cpb_installed' );
	}

	/**
	 * See if we need to show or run database updates during install.
	 *
	 * @since 3.2.0
	 */
	private static function maybe_update_db_version() {
		if ( self::needs_db_update() ) {
			CPB_Admin_Notices::add_notice( 'cpb-update' );
		} else {
			self::update_db_version();
		}
	}


	/**
	 * Is a DB update needed?
	 *
	 * @since  3.2.0
	 * @return boolean
	 */
	public static function needs_db_update() {
		$current_db_version = get_option( 'cpb_db_version', null );
		$updates            = self::get_db_update_callbacks();
		$update_versions    = array_keys( $updates );
		usort( $update_versions, 'version_compare' );
	  
		return is_null( $current_db_version ) && version_compare( $current_db_version, end( $update_versions ), '<' );
	}

	/**
	 * Update CPB version to current.
	 */
	private static function update_cpb_version() {
		// error_log("Update 1 :: ".get_option( 'cpb_revamped_db_updated', null ));
		// if ( ! is_null( get_option( 'cpb_revamped_db_updated', null ) ) ) {
			// delete_option( 'cpb_version' );
			// add_option( 'cpb_version', CPB()->version );
		// }
	}

	/**
	 * Update DB version to current.
	 *
	 * @param string|null $version New CPB DB version or null.
	 */
	public static function update_db_version( $version = null ) {
		delete_option( 'cpb_db_version' );
		add_option( 'cpb_db_version', is_null( $version ) ? CPB()->version : $version );
	}

	/**
	 * Install actions when a update button is clicked within the admin area.
	 *
	 * This function is hooked into admin_init to affect admin only.
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_cpb'] ) ) { // WPCS: input var ok.
			check_admin_referer( 'cpb_db_update', 'cpb_db_update_nonce' );
			self::update();
			CPB_Admin_Notices::add_notice( 'cpb-update' );
		}
	}

	/**
	 * Push all needed DB updates to the queue for processing.
	 */
	private static function update() {
		$current_db_version = null == get_option( 'cpb_db_version' ) ? '3.0.1' : get_option( 'cpb_db_version' );
		// $current_db_version = get_option( 'cpb_db_version' );
		$loop               = 0;


		foreach ( self::get_db_update_callbacks() as $version => $update_callbacks ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				foreach ( $update_callbacks as $update_callback ) {
					WC()->queue()->schedule_single(
						time() + $loop,
						'cpb_run_update_callback',
						array(
							'update_callback' => $update_callback,
						),
						'cpb-db-updates'
					);
					$loop++;
				}
			}
		}
	}

	/**
	 * Run an update callback when triggered by ActionScheduler.
	 *
	 * @since 3.6.0
	 * @param string $callback Callback name.
	 */
	public static function run_update_callback( $callback ) {
		include_once dirname( __FILE__ ) . '/cpb-update-functions.php';

		if ( is_callable( $callback ) ) {
			self::run_update_callback_start( $callback );
			$result = (bool) call_user_func( $callback );
			self::run_update_callback_end( $callback, $result );
		}
	}

	public static function run_second_update_callback( $callback, $id ) {
		if ( ! isset( $GLOBAL['old_postmeta_keys'] ) ) {
			return;
		}

		include_once dirname( __FILE__ ) . '/cpb-update-functions.php';

		if ( is_callable( $callback ) ) {
			self::run_update_callback_start( $callback );
			$result = (bool) call_user_func( $callback, $id );
			self::run_update_callback_end( $callback, $result );
		}    }

	/**
	 * Triggered when a callback will run.
	 *
	 * @since 3.6.0
	 * @param string $callback Callback name.
	 */
	protected static function run_update_callback_start( $callback ) {
		wc_maybe_define_constant( 'CPB_UPDATING', true );
	}

	/**
	 * Triggered when a callback has ran.
	 *
	 * @since 3.6.0
	 * @param string $callback Callback name.
	 * @param bool   $result Return value from callback. Non-false need to run again.
	 */
	protected static function run_update_callback_end( $callback, $result ) {
		if ( $result ) {
			WC()->queue()->add(
				'cpb_run_update_callback',
				array(
					'update_callback' => $callback,
				),
				'cpb-db-updates'
			);
		}
	}


	/**
	 * Get list of DB update callbacks.
	 *
	 * @since  3.0.0
	 * @return array
	 */
	public static function get_db_update_callbacks() {
		return self::$db_updates;
	}

	/**
	 * Default options.
	 *
	 * Sets up the default options used on the settings page.
	 */
	private static function create_options() {
		$msg_labels = get_label_string();

		// Default settings
		$cpb_settings = array(
		   'cpb_grand_total_label' => __( 'Grand Total', 'custom-product-boxes' ),
		   'cpb_enable_giftbox_total' => 'on',
		   'cpb_giftbox_total_label' => __( 'Box Total', 'custom-product-boxes' ),
		   'cpb_enable_addbox_total' => 'on',
		   'cpb_addbox_total_label' => __( 'Box Charges', 'custom-product-boxes' ),
		   'cpb_anonymize_msg' => 'off',
		   'cpb_hide_stock' => 'off',
		   'cpb_old_order_labels' => $msg_labels,
		);
					   
		foreach ( $cpb_settings as $key => $value ) {
			if ( $key == 'wdmcpb_old_order_labels' ) {
				$msg_array = array_map( 'trim', explode( ',', $value ) );
				add_option( 'wdmcpb_old_order_labels', $msg_array );
				continue;
			}
			add_option( $key, $value );
		}
	}

	/**
	 * Check CPB version and run the updater if required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */
	public static function check_cpb_version() {
		$current_version = get_option( 'cpb_version', null );
		if ( ! defined( 'IFRAME_REQUEST' ) && is_null( $current_version ) && version_compare( $current_version, CPB()->version, '<' ) ) {
			self::install();
			do_action( 'cpb_updated' );
		}
	}

	private static function migrate_options() {
		$new_options_keys = array(
			'_wdm_enable_giftbox_total' => 'cpb_enable_giftbox_total',
			'_wdm_enable_addbox_total'  => 'cpb_enable_addbox_total',
			'_wdmcpb_anonymize_mgs'     => 'cpb_anonymize_msg',
			'_wdm_hide_stock'           => 'cpb_hide_stock',
			'wdmcpb_old_order_labels'   => 'cpb_old_order_labels',
			'_grand_total_label'        => 'cpb_grand_total_label',
			'_giftbox_total_label'      => 'cpb_giftbox_total_label',
			'_addbox_total_label'       => 'cpb_addbox_total_label',
		);

		$cpb_settings = array();

		foreach ($new_options_keys as $key => $value) {
			if ( get_option( $key ) ) {
				$cpb_settings[ $value ] = get_option( $key );
			}
		}

		add_option( 'cpb_settings', $cpb_settings );
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *      cpb_prefilled_products_data - Table for storing prefilled products data.
	 */
	private static function create_tables() {
		global $wpdb;

		$collate = self::get_wp_charset_collate();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$wpdb->hide_errors();
		
		$cpb_prefilled_products = $wpdb->prefix . 'cpb_prefilled_products_data';            
		if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$cpb_prefilled_products';" ) ) {
			$prefilled_table_query  = "
			CREATE TABLE IF NOT EXISTS {$cpb_prefilled_products} (
								id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
								cpb_product_id bigint(20),
								unique_prod_id varchar(35),
								prefill_product_id bigint(20),
								prefill_quantity bigint(20),
								prefill_mandatory TINYINT(1),
								product_type varchar(35),
								INDEX product_id (cpb_product_id),
								INDEX unique_id (unique_prod_id),
								INDEX user_id (prefill_product_id)
							) $collate;
							";
			@dbDelta( $prefilled_table_query );
		}

		self::alter_table();
	}

	/**
	* Gets the default charset and collate for the MySQL database.
	* @return string $charset_collate charset and collate for the MySQL 
	* database.
	*/
	protected static function get_wp_charset_collate() {
		global $wpdb;
		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		return $charset_collate;
	}

	public static function alter_table() {
		global $wpdb;
		$cpb_prefilled_products = $wpdb->prefix . 'cpb_prefilled_products_data';
		if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `$cpb_prefilled_products` LIKE 'unique_prod_id';" ) ) {
			$wpdb->query( "ALTER TABLE $cpb_prefilled_products ADD unique_prod_id varchar(35) NOT NULL DEFAULT '', ADD INDEX unique_id (unique_prod_id)" );
		}

		if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `$cpb_prefilled_products` LIKE 'product_type';" ) ) {
			$wpdb->query( "ALTER TABLE $cpb_prefilled_products ADD product_type varchar(35) NOT NULL DEFAULT 'simple'" );
		}
	}
}

CPB_Install::init();
