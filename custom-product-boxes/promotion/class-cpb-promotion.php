<?php
/**
 * WisdmLabs Other Extensions
 *
 * To register the Quick Course Module.
 *
 * @package Advanced_Modules
 *
 * @since   1.0
 *
 *
 */
if ( ! class_exists( 'CPB_Promotion' ) ) {

    class CPB_Promotion {
        private static $instance = null;

        /**
         * Private constructor to make this a singleton
         *
         * @access private
         */
        public function __construct() {
            add_action( 'cpb_other_extensions', array( $this, 'cpb_promotion_page' ) );
        }
        /**
         * Function to instantiate our class and make it a singleton
         */
        public static function get_instance() {
            if ( ! self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }


        public function cpb_promotion_page() {
            if ( false === ( $extensions = get_transient( '_wdmcpb_extensions_data' ) ) ) {
                $extensions_json = wp_remote_get(
                    'https://wisdmlabs.com/products-thumbs/woo_extension.json',
                    array(
                        'user-agent' => 'Woocommerce Extensions Page'
                    )
                );

                if ( ! is_wp_error( $extensions_json ) ) {
                    $extensions = json_decode( wp_remote_retrieve_body( $extensions_json ) );

                    if ( $extensions ) {
                        set_transient( '_wdmcpb_extensions_data', $extensions, 72 * HOUR_IN_SECONDS );
                    }
                }
            }
            include_once( 'partials/other-extensions.php' );
            unset( $extensions );
        }

        // End of functions
    }
}
