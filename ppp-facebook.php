<?php
/*
Plugin Name: Post Promoter Pro - Facebook
Plugin URI: https://www.postpromoterpro.com
Description: Schedule your Facebook Posts
Version: 1.0
Author: Chris Klosowski
Author URI: http://www.kungfugrep.com
License: GPLv2 or later
*/

class PPP_Facebook_Posts {
	private static $instance;

	public static function getInstance() {
		if( !self::$instance ) {
			self::$instance = new PPP_Facebook_Posts();
			if ( self::$instance->verify_config() ) {
				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->load_textdomain();
				self::$instance->hooks();
			}
		}

		return self::$instance;
	}

	/**
	 * Verify conditions are set for General Sharing to work correctly
	 *
	 * @access private
	 * @since  1.0.0
	 * @return bool If Send Cart can work with the current site configuration
	 */
	private function verify_config() {
		$config_valid = true;

		if ( ! defined( 'PPP_PATH' ) || ! function_exists( 'ppp_facebook_enabled' ) || ! ppp_facebook_enabled() ) {
			add_action( 'admin_notices', array( $this, 'ppp_not_present' ) );
			$config_valid = false;
		}

		return $config_valid;
	}

	/**
	 * Display a notice when PPP isn't active
	 * @return void
	 */
	public function ppp_not_present() {
		echo '<div class="error"><p>' . __( 'Post Promoter Pro - Facebook requires Post Promoter Pro. Please activate it and enable Facebook.', 'ppp-gs-text' ) . '</p></div>';
	}

	/**
	 * Setup plugin constants
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function setup_constants() {
		// Plugin version
		define( 'PPP_FACEBOOK_VERSION', '1.0.0' );

		// Plugin path
		define( 'PPP_FACEBOOK_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin URL
		define( 'PPP_FACEBOOK_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Include necessary files
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 */
	private function includes() {
		// Include scripts
		require_once PPP_FACEBOOK_DIR . 'includes/post-type.php';
		require_once PPP_FACEBOOK_DIR . 'includes/scripts.php';
		require_once PPP_FACEBOOK_DIR . 'includes/functions.php';
		require_once PPP_FACEBOOK_DIR . 'includes/meta-boxes.php';
		require_once PPP_FACEBOOK_DIR . 'includes/actions.php';
		require_once PPP_FACEBOOK_DIR . 'includes/filters.php';
	}

	/**
	 * Internationalization
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function load_textdomain() {
		// Set filter for language directory
		$lang_dir = PPP_FACEBOOK_DIR . '/languages/';
		$lang_dir = apply_filters( 'ppp_facebook_langs_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'ppp_facebook_locale', get_locale(), 'ppp-facebook-txt' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'ppp-facebook', $locale );

		// Setup paths to current locale file
		$mofile_local   = $lang_dir . $mofile;
		$mofile_global  = WP_LANG_DIR . '/ppp-facebook/' . $mofile;

		if( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/ppp-facebook/ folder
			load_textdomain( 'ppp-facebook-txt', $mofile_global );
		} elseif( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/ppp-facebook/languages/ folder
			load_textdomain( 'ppp-facebook-txt', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'ppp-facebook-txt', false, $lang_dir );
		}
	}

	/**
	 * Run action and filter hooks
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      void
	 *
	 */
	private function hooks() {

		// Flush rule son activation for the custom post type
		register_activation_hook( __FILE__, array( 'PPP_Facebook_Posts', 'activation' ) );

	}

	public static function activation() {

		flush_rewrite_rules();

	}

}

function ppp_load_facebook() {
	$PPP_Facebook_Posts = PPP_Facebook_Posts::getInstance();
}
add_action( 'plugins_loaded', 'ppp_load_facebook', 99 );
