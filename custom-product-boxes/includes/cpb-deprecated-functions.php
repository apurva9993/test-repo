<?php
/**
 * CPB Functions
 *
 * Deprecated functions here.
 *
 * @package  CPB\Functions
 * @version  2.5.0
 */

/**
 * Deprecated functions here.
 *
 * @param  string $message     HTML string to display in notice.
 * @param  string $notice_type type of notice.
 * @return void
 */
function wdm_pb_bundles_add_notice( $message, $notice_type ) {
	cpb_print_admin_notices( $message, $notice_type );// with deprecation notice.
}
