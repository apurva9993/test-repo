<?php
/**
 * CPB setup
 *
 * @package CPB
 */

defined( 'ABSPATH' ) || exit;

use \Licensing\WdmLicense as WdmLicense;
use CPB_Install as CPB_Install;
/**
 * Main CPB Class.
 *
 * @class CPB
 */
final class Custom_Product_Boxes {
	/**
	 * CPB plugin version.
	 *
	 * @var string
	 */
	public $version = '4.0.0';

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	public $plugin_name = 'Custom Product Boxes';

	/**
	 * The single instance of the class.
	 *
	 * @var cpb_instance
	 * @since 2.1
	 */
	protected static $instance = null;


	/**
	 * The single instance of the class.
	 *
	 * @var WC_Product_Wdm_Bundle_Product
	 *
	 * @since 5.0.0
	 */
	protected static $cpb_product = array();

	/**
	 * Main WooCommerce Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WC()
	 * @return WooCommerce - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Main CPB_Display_Product instance. Ensures only one instance of CPB_Display_Product is loaded or can be loaded.
	 *
	 * @since  5.0.0
	 *
	 * @return CPB_Display_Product
	 */
	public function get_cpb_product( $product_id ) {
		if ( ! isset( self::$cpb_product[ $product_id ] ) ) {
			self::$cpb_product[ $product_id ] = new WC_Product_Wdm_Bundle_Product( $product_id );
		}
		return self::$cpb_product[ $product_id ];
	}

	/**
	 * Class Constructor.
	 */
	public function __construct() {
		if ( ! defined( 'WC_PLUGIN_FILE' ) ) {
			add_action( 'admin_notices', array( $this, 'cpb_base_plugin_inactive_notice' ) );
			return;
		}

		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 2.3
	 */
	private function init_hooks() {
		if ( get_option( 'cpb_run_install' ) ) {
			CPB_Install::install();
		}
		$this->register_product_type();
		$this->on_cpb_loaded();

		add_action( 'after_setup_theme', array( $this, 'cpb_include_template_functions' ), 11 );

		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Registers the Custom Product Boxes Product type to WooCommerce
	 *
	 * @return void
	 */
	public function register_product_type() {
		// CPB Product class. Registers our custom product type.
		require_once CPB_ABSPATH . 'includes/class-wc-product-wdm-bundle-product.php';

		// Data Store classes.
		include_once CPB_ABSPATH . 'includes/data/class-cpb-data.php';
	}

	/**
	 * Load Plugins License.
	 *
	 * @return void
	 */
	public function on_cpb_loaded() {
		global $cpb_plugin_data;
		$cpb_plugin_data = include_once( CPB_ABSPATH . 'license.config.php' );
		require_once CPB_ABSPATH . 'licensing/class-wdm-license.php';
		new WdmLicense( $cpb_plugin_data );

		if ( class_exists( 'WC_Subscriptions_Product' ) ) {
			include_once CPB_ABSPATH . 'includes/subscription/class-cpb-subscriptions-product.php';
		}
	}

	public function cpb_base_plugin_inactive_notice() {
		?>
		<div class="notice notice-error">
			<p>
				<?php
					$install_wc_url = admin_url( 'plugin-install.php?s=woocommerce&tab=search' );

					printf(
						__(
							'The <strong>%1$s</strong> requires WooCommerce to be activated ! <a href="%2$s">Install / Activate WooCommerce</a>'
						),
						$this->plugin_name,
						$install_wc_url
					);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Define CPB Constants.
	 */
	private function define_constants() {
		$this->define( 'CPB_VERSION', '4.0' );
		$this->define( 'EDD_WCPB_ITEM_NAME', 'Custom Product Boxes' );
		$this->define( 'CPB_ABSPATH', dirname( CPB_PLUGIN_FILE ) . '/' );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		/**
		 * Class autoloader.
		 */
		include_once CPB_ABSPATH . 'includes/class-cpb-customizer-setting.php';

		include_once CPB_ABSPATH . 'includes/class-cpb-autoloader.php';

		// Including core cpb functions.
		include_once CPB_ABSPATH . 'includes/cpb-core-function.php';

		// Including core classes.
		include_once CPB_ABSPATH . 'includes/class-cpb-install.php';

		include_once CPB_ABSPATH . 'includes/prefilled/class-cpb-prefill-data-manager.php';

		if ( is_request( 'frontend' ) ) {
			$this->cpb_theme_support();
			include_once CPB_ABSPATH . 'public/class-cpb-public.php';
			include_once CPB_ABSPATH . 'public/cpb-template-hooks.php';
			include_once CPB_ABSPATH . 'includes/class-cpb-ajax.php';
		}

		if ( is_request( 'admin' ) ) {
			// Including admin classes.
			include_once CPB_ABSPATH . 'admin/class-cpb-admin.php';
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Init CPB when WordPress Initialises.
	 */
	public function init() {
		$this->load_plugin_textdomain();

		if ( session_id() == '' ) {
			session_start();
		}
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'custom-product-boxes', false, plugin_basename( dirname( CPB_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Function used to Init WooCommerce Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function cpb_include_template_functions() {
		include_once CPB_ABSPATH . 'public/cpb-template-functions.php';
	}

	/**
	 * Gets the active theme stylesheet of the system.
	 * Include the template which provide the active theme compatibility.
	 */
	public function cpb_theme_support() {
		switch ( get_template() ) {
			case 'shopkeeper':
				include_once CPB_ABSPATH . '/custom-product-boxes-shopkeeper-addon.php';
				break;
			case 'enfold':
				include_once CPB_ABSPATH . '/custom-product-boxes-enfold-addon.php';
				break;
			case 'flatsome':
				include_once CPB_ABSPATH . '/custom-product-boxes-flatsome-addon.php';
				break;
			case 'Avada':
				include_once CPB_ABSPATH . '/custom-product-boxes-avada-addon.php';
				break;
			case 'theretailer':
				include_once CPB_ABSPATH . '/custom-product-boxes-theretailer-addon.php';
				break;
			case 'depot':
				include_once CPB_ABSPATH . '/custom-product-boxes-depot-addon.php';
				break;
			case 'Atelier':
				include_once CPB_ABSPATH . '/custom-product-boxes-atelier-addon.php';
				break;
			case 'salient':
				include_once CPB_ABSPATH . '/custom-product-boxes-salient-addon.php';
				break;
			default:
				break;
		}
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', CPB_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( CPB_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function template_path() {
		return untrailingslashit( plugin_dir_path( CPB_PLUGIN_FILE ) ) . '/templates';
	}
}
