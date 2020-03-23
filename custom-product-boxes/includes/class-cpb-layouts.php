<?php
/**
 * CPB_Layouts class
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB/Layouts
 * @since    4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
* This class is responsible for the layout settings of single product page for CPB
* display.
* It gives the templates directories source name required, it gives the layout
* meta-key stored in database(i.e, the layout selected by the admin for that CPB in
* settings)
* It gives the templates directories for that specific layout in CPB Plugin.
*/

if ( ! class_exists( 'CPB_Layouts' ) ) {
	class CPB_Layouts {

		/**
		 * Stores the array of layouts, its path and label in settings
		 */
		public static $layout_settings;
		/**
		* Returns the column field meta key name based on the desktop layout
		* selected.
		* @param int $main_product_id CPB Product Id.
		* @return string meta-key for the column field of the desktop layout
		* selected.
		*/
		public static function get_column_field( $main_product_id ) {
			$layout = get_post_meta( $main_product_id, 'cpb_layout_selected', true );
			$selected_layout = basename( $layout );
			$grid_field = array_merge(
				apply_filters( 'wdm_columns_gift_layout_index', array() ),
				array(
					'vertical' => 'cpb_box_column_size',
					'vertical-right' => 'cpb_box_column_size',
					'horizontal' => 'cpb_box_row_size',
				)
			);

			return apply_filters( 'wdm_columns_gift_layout', $grid_field[ $selected_layout ] );
		}

		/**
		* Returns the product grid field meta key name based on the desktop layout
		* selected.
		* @param int $main_product_id CPB Product Id.
		* @return string meta-key for the product grid of the desktop layout
		* selected.
		*/
		public static function get_product_field( $main_product_id ) {
			$layout = get_post_meta( $main_product_id, 'cpb_layout_selected', true );
			$selected_layout = basename( $layout );
			$grid_field = array_merge(
				apply_filters( 'wdm_columns_product_layout_index', array() ),
				array(
					'vertical' => 'cpb_product_column_size',
					'vertical-right' => 'cpb_product_column_size',
					'horizontal' => '_wdm_product_item_grid',
				)
			);
			return apply_filters( 'wdm_columns_product_layout', $grid_field[ $selected_layout ] );
		}


		/* Gets the available CPB template directory from the theme and plugin */
		public static function get_layout_directories() {
			$layout_directories = apply_filters(
				'cpb_desktop_template_directories',
				array(
					plugin_dir_path( dirname( __FILE__ ) ) . 'templates/product-layouts/desktop-layouts/',
					get_stylesheet_directory() . '/custom-product-boxes/product-layouts/desktop-layouts/',
					get_template_directory() . '/custom-product-boxes/product-layouts/desktop-layouts/',
				)
			);

			return array_unique( $layout_directories );
		}

		/**
		* Gets all the template directories for CPB.
		* Gets the layout names for the template (i.e, source plugin/theme name.)
		* Returns the array of all the templates with the vertical template at top.
		* @return array $all_layouts array of all the layouts.
		*/
		public static function get_available_layouts() {
			$layout_directories = self::get_layout_directories();
			if ( empty( $layout_directories ) || ! is_array( $layout_directories ) ) {
				return;
			}

			$all_layouts = array();

			foreach ( $layout_directories as $layout_directory ) {
				$layouts = array_filter( glob( "{$layout_directory}*" ), 'is_dir' );
				foreach ( $layouts as $layout ) {
					$layout_name = self::get_layout_name( $layout );
					if ( ! empty( $layout_name ) ) {
						$all_layouts[ $layout ] = $layout_name;
					}
				}
			}
			//put vertical layout at top
			$vertical_layout_key = CPB()->plugin_path() . '/templates/product-layouts/desktop-layouts/vertical';
			$all_layouts = array( $vertical_layout_key => $all_layouts[ $vertical_layout_key ] ) + $all_layouts;
			return $all_layouts;
		}

		/**
		* Get the layout name for the template.
		* Gets the source type of the template (plugins/theme)
		* Get the source plugin/theme name.
		* @param string $layout path of the template layout.
		* @return string $layout_name layout name for the template.
		*/
		public static function get_layout_name( $layout ) {
			$layout_name = '';
			if ( file_exists( $layout . '/index.php' ) ) {
				$type = self::get_source_type( $layout );
				if ( empty( $type ) ) {
					return;
				}
				$source_name = self::get_source_name( $type, $layout );
				$layout_data = implode( '', file( $layout . '/index.php' ) );
				if ( preg_match( '|Template Name:(.*)$|mi', $layout_data, $name ) ) {
					$layout_name = sprintf( __( '%1$s | Source Type: %2$s | Source: %3$s', 'custom-product-boxes' ), _cleanup_header_comment( $name[1] ), ucfirst( $type ), $source_name );
				}
			}

			return $layout_name;
		}

		/**
		* Gets the source plugin or theme name for the template.
		* @param string $type source type of template (plugins/themes)
		* @param string $layout path of the template layout.
		* @return string $source_name source plugin/theme name
		*/
		public static function get_source_name( $type, $layout ) {
			switch ( $type ) {
				case 'plugins':
					$source_name = self::get_plugin_name( $layout );
					return $source_name;
				case 'themes':
					$source_name = self::get_theme_name( $layout );
					return $source_name;
			}
		}
		/**
		* Gets the array of presently activated plugins.
		* @return array $plugins active plugins.
		*/
		public static function get_active_plugins() {
			static $plugins;
			if ( ! isset( $plugins ) ) {
				$plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			}

			return $plugins;
		}

		/**
		* Returns the plugin name(for the souce plugin).
		* @param string $layout path of the template layout.
		* @return string Source Plugin name.
		*/
		public static function get_plugin_name( $layout ) {
			$plugins = self::get_active_plugins();
			if ( ! is_callable( 'get_plugins' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$all_plugins = get_plugins();

			foreach ( $plugins as $plugin_dir ) {
				$offset = strpos( $plugin_dir, '/' );
				$compare = substr( $plugin_dir, 0, $offset );
				if ( strpos( $layout, $compare ) !== false ) {
					return $all_plugins[ $plugin_dir ]['Name'];
				}
			}
		}

		/**
		* Returns the theme name(for the souce theme).
		* Gets the theme directories and then find the theme name.
		* @param string $layout path of the template layout.
		* @return string Source theme name.
		*/
		public static function get_theme_name( $layout ) {
			$offset = strpos( $layout, 'themes' );
			$theme_directories = substr( $layout, 0, strlen( 'themes' ) + $offset );
			$theme_directories = glob( $theme_directories . '/*', GLOB_ONLYDIR );

			foreach ( $theme_directories as $theme_dir ) {
				$position = strpos( $layout, $theme_dir );
				if ( false !== $position ) {
					$position += strlen( $theme_dir );
					$sub_str = substr( $layout, $position, 1 );
					if ( '/' === $sub_str ) {
						return self::get_theme_source_name( $theme_dir );
					}
				}
			}
			return $layout;
		}

		/**
		* Returns the theme name(for the souce theme).
		* @param string $theme_name path of the template layout.
		* @return string Source theme name.
		*/
		public static function get_theme_source_name( $theme_dir ) {
			$theme_data = implode( '', file( $theme_dir . '/style.css' ) );
			if ( preg_match( '|Theme Name:(.*)$|mi', $theme_data, $name ) ) {
				$theme_name = sprintf( __( '%s Theme', 'custom-product-boxes' ), _cleanup_header_comment( $name[1] ) );
				return $theme_name;
			}
		}

		/**
		* Gets the source type for each template(whether plugins/themes/mu-plugins)
		* @param string $layout templates layout folder path string.
		* @return string $source_type Source type of the template
		*/
		public static function get_source_type( $layout ) {
			$source_types = array( 'plugins', 'themes', 'mu-plugins' );

			foreach ( $source_types as $source_type ) {
				if ( strpos( $layout, $source_type ) !== false ) {
					return $source_type;
				}
			}
		}

		public static function get_layouts() {
			self::$layout_settings = array(
				'horizontal_legacy' => array(
					'path' => CPB()->template_path() . '/product-layouts/desktop-layouts/horizontal',
					'name' => __( 'Horizontal Legacy Layout', 'custom-product-boxes' ),
				),
				'vertical_left_legacy'   => array(
					'path' => CPB()->template_path() . '/product-layouts/desktop-layouts/vertical',
					'name' => __( 'Vertical Legacy Left Layout', 'custom-product-boxes' ),
				),
				'vertical_right_legacy'   => array(
					'path' => CPB()->template_path() . '/product-layouts/desktop-layouts/vertical-right',
					'name' => __( 'Vertical Legacy Right Layout', 'custom-product-boxes' ),
				),
				'horizontal'    => array(
					'path' => CPB()->template_path() . '/product-layouts/desktop-layouts/cpb_new_layout_vertical',
					'name' => __( 'Horizontal 4.0 Layout', 'custom-product-boxes' ),
				),
				'vertical'  => array(
					'path' => CPB()->template_path() . '/product-layouts/desktop-layouts/cpb_new_layout_vertical',
					'name' => __( 'Vertical 4.0 Layout', 'custom-product-boxes' ),
				),
			);

			$ctr = 1;
			$all_layouts = CPB_Layouts::get_available_layouts();

			foreach ( $all_layouts as $layout_path => $layout_name ) {
				if ( strpos( $layout_path, CPB()->template_path() ) == false ) {
					self::$layout_settings[ 'custom_layout_' . $ctr ]['path'] = $layout_path;
					self::$layout_settings[ 'custom_layout_' . $ctr ]['name'] = $layout_name;
					self::$layout_settings[ 'custom_layout_' . $ctr ]['notice'] = __( 'Please update your template.', 'custom-product-boxes' );
				}
			}

			return self::$layout_settings;
		}

		public static function format_layout_choices() {
			$layout_settings = self::get_layouts();
			$layout_choices = array();

			foreach ( $layout_settings as $settings_key => $value ) {
				$layout_choices[ $settings_key ] = $value['name'];
			}

			return $layout_choices;
		}
	}
}
