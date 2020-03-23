<?php
/**
 * This file contains the class that contains all the customizer settings customizations.
 *
 * @package CPB/Customizer
 */

if ( ! class_exists( 'CPB_Customizer_Setting' ) ) {
	/**
	 * This class handles all the action and filter hooks used for customizer customizations
	 *
	 * @author WisdmLabs
	 * @since 4.0
	 * @package CPB
	 */
	class CPB_Customizer_Setting {
		/**
		 * Instance of the class.
		 *
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Constructor.
		 */
		private function __construct() {
			$this->init_hooks();
		}

		/**
		 * This method is used to add action/filter hooks.
		 */
		public function init_hooks() {
			add_action( 'customize_register', array( $this, 'add_customizer_section' ) );
			add_action( 'wp_head', array( $this, 'apply_customizer_styles' ), 999 );
		}

		/**
		 * Returns an instance of this class.
		 *
		 * @return object of this class
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Responsible to apply styles changed from customizer form API call.
		 */
		public function apply_customizer_styles() {

			$layout_float = apply_filters(
				'cpb_vertical_empty_box_position',
				array(
					'right' => array(
						'style' => '.cpb-product-box-wrap-container{
									float: right;
								}
								.cpb-products-wrap-container{
									float: left;
								}',
						'is_enabled' => get_option( 'cpb_vertical_empty_box_position' ) == 'right',
					),
					'left' => array(
						'style' => '.cpb-product-box-wrap-container{
									float: left;
								}
								.cpb-products-wrap-container{
									float: right;
								}',
						'is_enabled' => get_option( 'cpb_vertical_empty_box_position' ) == 'left',
					),
				)
			);

			$customizer_style = array(
				'cpb_grid_width' => array(
					'style' => '.cpb-image, .cpb-empty-box-inner .cpb-product-image img{
								width: ' . esc_html( get_option( 'cpb_grid_width', '125' ) ) . 'px;
							}',
					'is_enabled' => get_option( 'cpb_grid_width' ),
				),
				'cpb_vertical_empty_boxes_height' => array(
					'style' => '.box-full-msg, .cpb-product-box-wrap{
								height: ' . esc_html( get_option( 'cpb_vertical_empty_boxes_height' ) ) . 'px;
							}',
					'is_enabled' => get_option( 'cpb_vertical_empty_boxes_height' ),
				),
				'cpb_vertical_addon_boxes_height' => array(
					'style' => '.cpb-products-wrap{
								height: ' . esc_html( get_option( 'cpb_vertical_addon_boxes_height' ) ) . 'px;
							}',
					'is_enabled' => get_option( 'cpb_vertical_addon_boxes_height' ),
				),
				'cpb_vertical_progress_color' => array(
					'style' => '.cpb-filled-part{
								background-color: ' . esc_html( get_option( 'cpb_vertical_progress_color' ) ) . ';
							}',
					'is_enabled' => get_option( 'cpb_vertical_progress_color' ),
				),
				'cpb_vertical_product_title_color' => array(
					'style' => '.cpb-product-title{
								color: ' . esc_html( get_option( 'cpb_vertical_product_title_color' ) ) . ';
							}',
					'is_enabled' => get_option( 'cpb_vertical_product_title_color' ),
				),
				'cpb_vertical_product_stock_status' => array(
					'style' => '.cpb-stock-status, .cpb-stock-status p{
								color: ' . esc_html( get_option( 'cpb_vertical_product_stock_status' ) ) . ' !important;
							}',
					'is_enabled' => get_option( 'cpb_vertical_product_stock_status' ),
				),
				'cpb_vertical_product_price' => array(
					'style' => '.cpb-product-price span, .cpb-product-price{
								color: ' . esc_html( get_option( 'cpb_vertical_product_price' ) ) . ' !important;
							}',
					'is_enabled' => get_option( 'cpb_vertical_product_price' ),
				),
				'cpb_vertical_empty_boxes_wrap_bg' => array(
					'style' => '.cpb-product-box-wrap{
								background-color: ' . esc_html( get_option( 'cpb_vertical_empty_boxes_wrap_bg' ) ) . ';
							}
							.cpb-product-box-wrap{
								scrollbar-color: ' . esc_html( get_option( 'cpb_vertical_empty_boxes_wrap_bg' ) ) . esc_html( get_option( 'cpb_vertical_empty_boxes_wrap_bg' ) ) . ';
							}',
					'is_enabled' => get_option( 'cpb_vertical_empty_boxes_wrap_bg' ),
				),
				'cpb_vertical_product_boxes_wrap_bg' => array(
					'style' => '.cpb-products-wrap{
								background-color: ' . esc_html( get_option( 'cpb_vertical_product_boxes_wrap_bg' ) ) . ';
							}
							.cpb-products-wrap{
								scrollbar-color: ' . esc_html( get_option( 'cpb_vertical_product_boxes_wrap_bg' ) ) . esc_html( get_option( 'cpb_vertical_product_boxes_wrap_bg' ) ) . ';
							}',
					'is_enabled' => get_option( 'cpb_vertical_product_boxes_wrap_bg' ),
				),
				'cpb_vertical_empty_box_item_bg' => array(
					'style' => '.cpb-empty-box-inner{
								background-color: ' . esc_html( get_option( 'cpb_vertical_empty_box_item_bg' ) ) . ';
							}',
					'is_enabled' => get_option( 'cpb_vertical_empty_box_item_bg' ),
				),
				'cpb_vertical_boxes_width' => array(
					'style' => '.cpb-product-inner, .cpb-product-image img, .cpb-image{
								width: ' . esc_html( get_option( 'cpb_vertical_boxes_width' ) ) . 'px;
							}',
					'is_enabled' => get_option( 'cpb_vertical_boxes_width' ),
				),
				'cpb_vertical_box_item_height' => array(
					'style' => '.cpb-empty-box-inner{
								min-height: ' . esc_html( get_option( 'cpb_vertical_box_item_height' ) ) . 'px;
							}',
					'is_enabled' => get_option( 'cpb_vertical_box_item_height' ),
				),
				'cpb_vertical_boxes_spacing' => array(
					'style' => '.cpb-product-box-wrap, .cpb-products-wrap{
								padding: ' . esc_html( get_option( 'cpb_vertical_boxes_spacing' ) ) . 'px;
							}',
					'is_enabled' => get_option( 'cpb_vertical_boxes_spacing' ),
				),
				'cpb_vertical_box_item_spacing' => array(
					'style' => '.cpb-product-inner, .cpb-empty-box-inner{
								margin: ' . esc_html( get_option( 'cpb_vertical_box_item_spacing' ) ) . 'px;
							}',
					'is_enabled' => get_option( 'cpb_vertical_box_item_spacing' ),
				),
				'cpb_layout_type' => array(
					'style' => '.cpb-product-box-wrap-container, .cpb-products-wrap-container, .progress-wrap{
								width: 100%;
							}
							.cpb-product-box-wrap-container{
								margin-bottom: 30px;
							}',
					'is_enabled' => get_option( 'cpb_layout_type' ) == 'horizontal',
				),
				'cpb_vertical_empty_box_position' => $layout_float[ get_option( 'cpb_vertical_empty_box_position', 'right' ) ],
			);
			?>
			<style type="text/css" id="cpb-customizer-styles">
				<?php
				foreach ( $customizer_style as $style ) {
					if ( $style['is_enabled'] ) {
						echo $style['style']; // @codingStandardsIgnoreLine.
					}
				}
				?>
			</style>
			<?php
		}

		/**
		 * Adds sections to CPB customizer area.
		 *
		 * @param object $wp_customize Object of WP_Customize class.
		 */
		public function add_customizer_section( $wp_customize ) {

			$this->cpb_add_customizer_panel( $wp_customize );

			$this->cpb_add_customizer_sections( $wp_customize );

			$this->cpb_add_customizer_settings( $wp_customize );

			$this->cpb_add_customizer_controls( $wp_customize );
		}

		/**
		 * Add customizer sections Controls.
		 * @param object $wp_customize Object of WP_Customize class.
		 * @return [type]               [description]
		 */
		public function cpb_add_customizer_controls( $wp_customize ) {
			// Layout type settings control.
			$this->cpb_add_layout_type_controls( $wp_customize );

			// New Layout settings controls.
			$this->cpb_add_new_layout_controls( $wp_customize );

			// Legacy layout settings.
			$this->cpb_add_legacy_layout_controls( $wp_customize );
		}

		public function cpb_add_legacy_layout_controls( $wp_customize ) {
			$wp_customize->add_control(
				'cpb_box_column_size',
				array(
					'type' => 'select',
					'section' => 'cpb_legacy_settings',
					'settings'  => 'cpb_box_column_size',
					'label' => __( 'Columns in Gift Box', 'custom-product-boxes' ),
					'choices' => apply_filters(
						'cpb_box_column_classes',
						array(
							'cpb-box-col-2' => __( '2', 'custom-product-boxes' ),
							'cpb-box-col-3' => __( '3', 'custom-product-boxes' ),
						)
					),
				)
			);
			$wp_customize->add_control(
				'cpb_product_column_size',
				array(
					'type' => 'select',
					'label'     => __( 'Columns in Product Layout', 'custom-product-boxes' ),
					'settings'  => 'cpb_product_column_size',
					'section'   => 'cpb_legacy_settings',
					'choices' => apply_filters(
						'cpb_product_column_classes',
						array(
							'cpb-product-col-2' => __( '2', 'custom-product-boxes' ),
							'cpb-product-col-3' => __( '3', 'custom-product-boxes' ),
						)
					),
				)
			);
			$wp_customize->add_control(
				'cpb_box_row_size',
				array(
					'type' => 'select',
					'label'     => __( 'Items per row in Gift Box (Recomended 8 for large box quantity)', 'custom-product-boxes' ),
					'settings'  => 'cpb_box_row_size',
					'section'   => 'cpb_legacy_settings',
					'choices' => apply_filters(
						'cpb_product_row_classes',
						array(
							'cpb-box-row-4' => __( '4', 'custom-product-boxes' ),
							'cpb-box-row-5' => __( '5', 'custom-product-boxes' ),
							'cpb-box-row-6' => __( '6', 'custom-product-boxes' ),
							'cpb-box-row-7' => __( '7', 'custom-product-boxes' ),
							'cpb-box-row-8' => __( '8', 'custom-product-boxes' ),
						)
					),
				)
			);
			$wp_customize->add_control(
				'cpb_product_row_size',
				array(
					'type' => 'select',
					'label'     => __( 'Items per row in Product Layout', 'custom-product-boxes' ),
					'settings'  => 'cpb_product_row_size',
					'section'   => 'cpb_legacy_settings',
					'choices' => apply_filters(
						'cpb_product_row_classes',
						array(
							'cpb-product-row-4' => __( '4', 'custom-product-boxes' ),
							'cpb-product-row-5' => __( '5', 'custom-product-boxes' ),
							'cpb-product-row-6' => __( '6', 'custom-product-boxes' ),
							'cpb-product-row-7' => __( '7', 'custom-product-boxes' ),
							'cpb-product-row-8' => __( '8', 'custom-product-boxes' ),
						)
					),
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_gift_bgcolor',
					array(
						'label'      => __( 'Background color for Gift box', 'custom-product-boxes' ),
						'section'    => 'cpb_legacy_settings',
						'settings'   => 'cpb_gift_bgcolor',
					)
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_gift_boxes_color',
					array(
						'label'      => __( 'Color for Gift boxes', 'custom-product-boxes' ),
						'section'    => 'cpb_legacy_settings',
						'settings'   => 'cpb_gift_boxes_color',
					)
				)
			);

			$wp_customize->add_control(
				'cpb_disable_scroll',
				array(
					'type' => 'checkbox',
					'section' => 'cpb_legacy_settings',
					'settings'  => 'cpb_disable_scroll',
					'label' => __( 'Allow Scroll Lock', 'custom-product-boxes' ),
					'description' => __( 'Enables the scroll of gift box', 'custom-product-boxes' ),
				)
			);
		}

		/**
		 * Add LAyout type Controls settings.
		 * @param object $wp_customize Object of WP_Customize class.
		 * @return [type]               [description]
		 */
		public function cpb_add_layout_type_controls( $wp_customize ) {
			$wp_customize->add_control(
				'cpb_layout_type',
				array(
					'label'     => __( 'Layout Types', 'custom-product-boxes' ),
					'settings'  => 'cpb_layout_type',
					'section'   => 'cpb_layout_section',
					// 'priority'  => 1,
					'type'      => 'radio',
					'choices'   => CPB_Layouts::format_layout_choices(),
				)
			);
		}

		/**
		 * Add New Layout Controls settings.
		 * @param object $wp_customize Object of WP_Customize class.
		 * @return [type]               [description]
		 */
		public function cpb_add_new_layout_controls( $wp_customize ) {
			$wp_customize->add_control(
				'cpb_vertical_empty_boxes_height',
				array(
					'type' => 'number',
					'label'     => 'Empty Box Height',
					'settings'  => 'cpb_vertical_empty_boxes_height',
					'section'   => 'cpb_new_settings_section',
					// 'priority'  => 1,
					'input_attrs' => array(
						'min' => 150,
						'max' => 900,
						'step' => 5,
					),
				)
			);
			$wp_customize->add_control(
				'cpb_vertical_box_item_height',
				array(
					'type' => 'number',
					'section' => 'cpb_new_settings_section',
					'settings'  => 'cpb_vertical_box_item_height',
					'label' => __( 'Empty Box Item Height' ),
					'input_attrs' => array(
						'min' => 70,
						'max' => 500,
						'step' => 5,
					),
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_vertical_empty_boxes_wrap_bg',
					array(
						'label'      => __( 'Empty Box Background', 'custom-product-boxes' ),
						'section'    => 'cpb_new_settings_section',
						'settings'   => 'cpb_vertical_empty_boxes_wrap_bg',
					)
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_vertical_empty_box_item_bg',
					array(
						'label'      => __( 'Empty Box Item Background', 'custom-product-boxes' ),
						'section'    => 'cpb_new_settings_section',
						'settings'   => 'cpb_vertical_empty_box_item_bg',
					)
				)
			);
			$wp_customize->add_control(
				'cpb_vertical_empty_box_position',
				array(
					'type' => 'radio',
					'section' => 'cpb_new_settings_section',
					'settings'  => 'cpb_vertical_empty_box_position',
					'label' => __( 'Empty Box Position', 'custom-product-boxes' ),
					'choices'   => array(
						'left' => 'Left',
						'right' => 'Right',
					),
				)
			);
			$wp_customize->add_control(
				'cpb_vertical_hide_scroll_indicator',
				array(
					'type' => 'checkbox',
					'section' => 'cpb_new_settings_section',
					'settings'  => 'cpb_vertical_hide_scroll_indicator',
					'label' => __( 'Hide Scroll Indicator for Addon/Empty Box', 'custom-product-boxes' ),
				)
			);
			$wp_customize->add_control(
				'cpb_vertical_addon_boxes_height',
				array(
					'type' => 'number',
					'label'     => __( 'Products Box Height', 'custom-product-boxes' ),
					'settings'  => 'cpb_vertical_addon_boxes_height',
					'section'   => 'cpb_new_settings_section',
					// 'priority'  => 1,
					'input_attrs' => array(
						'min' => 150,
						'max' => 900,
						'step' => 5,
					),
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_vertical_product_boxes_wrap_bg',
					array(
						'label'      => __( 'Products Box Background', 'custom-product-boxes' ),
						'section'    => 'cpb_new_settings_section',
						'settings'   => 'cpb_vertical_product_boxes_wrap_bg',
					)
				)
			);
			$wp_customize->add_control(
				'cpb_vertical_boxes_width',
				array(
					'type' => 'number',
					'section' => 'cpb_new_settings_section',
					'settings'  => 'cpb_vertical_boxes_width',
					'label' => __( 'Box Item Width' ),
					'input_attrs' => array(
						'min' => 70,
						'max' => 500,
						'step' => 10,
					),
				)
			);
			$wp_customize->add_control(
				'cpb_vertical_boxes_spacing',
				array(
					'type' => 'number',
					'section' => 'cpb_new_settings_section',
					'settings'  => 'cpb_vertical_boxes_spacing',
					'label' => __( 'Box Spacing' ),
					'input_attrs' => array(
						'min' => 0,
						'max' => 30,
						'step' => 4,
					),
				)
			);
			$wp_customize->add_control(
				'cpb_vertical_box_item_spacing',
				array(
					'type' => 'number',
					'section' => 'cpb_new_settings_section',
					'settings'  => 'cpb_vertical_box_item_spacing',
					'label' => __( 'Box Item Spacing' ),
					'input_attrs' => array(
						'min' => 5,
						'max' => 20,
						'step' => 5,
					),
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_vertical_progress_color',
					array(
						'label'      => __( 'Progress Bar Color', 'custom-product-boxes' ),
						'section'    => 'cpb_new_settings_section',
						'settings'   => 'cpb_vertical_progress_color',
					)
				)
			);
			$wp_customize->add_control(
				'cpb_vertical_hide_expand',
				array(
					'type' => 'checkbox',
					'section' => 'cpb_new_settings_section',
					'settings'  => 'cpb_vertical_hide_expand',
					'label' => __( 'Hide Expand Button', 'custom-product-boxes' ),
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_vertical_product_title_color',
					array(
						'label'      => __( 'Add-on product title color', 'custom-product-boxes' ),
						'section'    => 'cpb_new_settings_section',
						'settings'   => 'cpb_vertical_product_title_color',
					)
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_vertical_product_stock_status',
					array(
						'label'      => __( 'Stock status color', 'custom-product-boxes' ),
						'section'    => 'cpb_new_settings_section',
						'settings'   => 'cpb_vertical_product_stock_status',
					)
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'cpb_vertical_product_price',
					array(
						'label'      => __( 'Add-on product price', 'custom-product-boxes' ),
						'section'    => 'cpb_new_settings_section',
						'settings'   => 'cpb_vertical_product_price',
					)
				)
			);
		}

		/**
		 * Add new layout customizer settings.
		 * @param object $wp_customize Object of WP_Customize class.
		 * @return [type]               [description]
		 */
		public function cpb_add_new_layout_settings( $wp_customize ) {
			$wp_customize->add_setting(
				'cpb_vertical_empty_boxes_height',
				array(
					'default'    => '520',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_box_item_height',
				array(
					'default'    => '150',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_empty_boxes_wrap_bg',
				array(
					'default'    => '#faebd7',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_hide_expand',
				array(
					'default'    => false,
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_boxes_width',
				array(
					'default'    => '125',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_product_title_color',
				array(
					'default'    => '#444444',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_product_stock_status',
				array(
					'default'    => '#77a464',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_boxes_spacing',
				array(
					'default'    => '20',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_box_item_spacing',
				array(
					'default'    => '15',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_progress_color',
				array(
					'default'    => '#D9CF75',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_product_price',
				array(
					'default'    => '#ee9823',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_empty_box_item_bg',
				array(
					'default'    => '#f9f9f9',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_empty_box_position',
				array(
					'default'    => 'right',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_hide_scroll_indicator',
				array(
					'default'    => false,
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_product_boxes_wrap_bg',
				array(
					'default'    => '#f0f8ff',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_vertical_addon_boxes_height',
				array(
					'default'    => '520',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
		}

		/**
		 * Add customizer Controls settings.
		 * @param object $wp_customize Object of WP_Customize class.
		 * @return [type]               [description]
		 */
		public function cpb_add_customizer_settings( $wp_customize ) {
			$this->cpb_add_new_layout_settings( $wp_customize );

			$wp_customize->add_setting(
				'cpb_layout_type',
				array(
					'type'       => 'option',
					'default' => 'vertical-legacy',
					'section' => 'cpb_layout_section',
					'capability' => 'edit_theme_options',
				)
			);
			$wp_customize->add_setting(
				'cpb_box_column_size',
				array(
					'default'    => 'cpb-box-col-2',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_product_column_size',
				array(
					'default'    => 'cpb-product-col-2',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_product_row_size',
				array(
					'default'    => 'cpb-product-row-4',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);

			$wp_customize->add_setting(
				'cpb_box_row_size',
				array(
					'default'    => 'cpb-box-row-4',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);

			$wp_customize->add_setting(
				'cpb_disable_scroll',
				array(
					'default'    => false,
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_gift_bgcolor',
				array(
					'default'    => '#faebd7',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
			$wp_customize->add_setting(
				'cpb_gift_boxes_color',
				array(
					'default'    => '#f2f2f2',
					'type'       => 'option',
					'capability' => 'edit_theme_options',
					'transport'     => 'refresh',
				)
			);
		}

		/**
		 * Add customizer Panel.
		 * @param object $wp_customize Object of WP_Customize class.
		 * @return [type]               [description]
		 */
		public function cpb_add_customizer_panel( $wp_customize ) {
			$wp_customize->add_panel(
				'cpb_panel',
				array(
					'title' => __( 'Custom Product Boxes', 'custom-product-boxes' ),
					// 'priority' => 160,
				)
			);
		}

		/**
		 * Add customizer Sections.
		 * @param object $wp_customize Object of WP_Customize class.
		 * @return [type]               [description]
		 */
		public function cpb_add_customizer_sections( $wp_customize ) {
			$wp_customize->add_section(
				'cpb_layout_section',
				array(
					'title' => 'Layout Type',
					'panel' => 'cpb_panel',
				)
			);
			$wp_customize->add_section(
				'cpb_new_settings_section',
				array(
					'title' => 'New Layout settings',
					'panel' => 'cpb_panel',
				)
			);
			$wp_customize->add_section(
				'cpb_legacy_settings',
				array(
					'title' => 'Legacy Layout settings',
					'panel' => 'cpb_panel',
				)
			);
		}
	}
	CPB_Customizer_Setting::get_instance();
}
