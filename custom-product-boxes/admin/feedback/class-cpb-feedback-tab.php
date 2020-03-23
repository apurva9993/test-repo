<?php
/**
 * Class for the Settings tab on the admin page.
 *
 * @package Feedback
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class for showing feedback in CPB
 */
class CPB_Feedback_Tab {
	/**
	 * Adds action for the Settings tab and saving the settings.
	 */
	public function __construct() {
		add_action( 'cpb_feedback_tab', array( $this, 'feedback_tab_callback' ) );
	}



	/**
	 * For the Settings tab
	 * Enqueues the scripts and styles.
	 */
	public function feedback_tab_callback() {
		self::enqueue_script();
		?>
		<div class="typeform-widget" data-url="https://wisdmlabs584994.typeform.com/to/DETbso" data-transparency="100" data-hide-headers=true data-hide-footer=true style="width: 100%; height: 500px;"></div>
		<script>
			( function() {
				var qs,js,q,s,d = document, gi = d.getElementById, ce = d.createElement, gt = d.getElementsByTagName, id = "typef_orm", b = "https://embed.typeform.com/";
				if( ! gi.call( d,id ) ) {
					js = ce.call( d,"script" );

					js.id = id;
					js.src = b+"embed.js";
					q = gt.call( d, "script" )[0];
					q.parentNode.insertBefore( js, q );
				}
			})();
		</script>
		<div style="font-family: Sans-Serif;font-size: 12px;color: #999;opacity: 0.5; padding-top: 5px;">
		powered by
			<a href="https://wisdmlabs584994.typeform.com/to/Wnj5b1" style="color: #999" target="_blank">Typeform</a>
		</div>
		<?php
	}

	/**
	 * Enqueue the scripts
	 */
	private function enqueue_script() {
		 // Bootstrap.
		wp_enqueue_style( 'cpb_bootstrap_css', CPB()->plugin_url() . '/assets/css/bootstrap.css', array(), CPB_VERSION );
	}
}
