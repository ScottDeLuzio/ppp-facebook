<?php

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @global      array $edd_settings_page The slug for the EDD settings page
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function edd_send_cart_admin_scripts_fb( $hook ) {
	global $post_type;

	// Use minified libraries if SCRIPT_DEBUG is turned off
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	if( $post_type == 'ppp_facebook' ) {
		wp_enqueue_style( 'jquery-chosen', PPP_FACEBOOK_URL . 'assets/css/chosen' . $suffix . '.css', array(), PPP_FACEBOOK_VERSION );
		wp_enqueue_script( 'jquery-chosen', PPP_FACEBOOK_URL . 'assets/js/chosen.jquery' . $suffix . '.js', array( 'jquery' ), PPP_FACEBOOK_VERSION );
		wp_enqueue_script( 'ppp-facebook-admin', PPP_FACEBOOK_URL . 'assets/js/admin-scripts' . $suffix . '.js', array( 'jquery' ), PPP_FACEBOOK_VERSION, true );
		wp_enqueue_style( 'ppp-facebook-admin-styles', PPP_FACEBOOK_URL . 'assets/css/admin-styles' . $suffix . '.css' );
	}
}
add_action( 'admin_enqueue_scripts', 'edd_send_cart_admin_scripts_fb', 100 );