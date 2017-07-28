<?php

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Register the metaboxes for Post Promoter Pro - Facebook
 * @return void
 */
function ppp_facebook_register_meta_box() {
	global $post;

	if ( $post->post_type !== 'ppp_facebook' ) {
		return;
	}

	add_meta_box( 'ppp_facebook_link_callback', __( 'Add a Link to this Post', 'ppp-facebook-txt' ), 'ppp_facebook_link_callback', 'ppp_facebook', 'normal', 'high' );
	add_meta_box( 'ppp_facebook_info_callback', __( 'Post Info', 'ppp-facebook-txt' ), 'ppp_facebook_info_callback', 'ppp_facebook', 'side', 'core' );

}
add_action( 'add_meta_boxes', 'ppp_facebook_register_meta_box', 12 );

/**
 * Display the Metabox for Post Promoter Pro - Facebook
 * @return void
 */
function ppp_facebook_link_callback() {
	global $post;
	$current_link = get_post_meta( $post->ID, '_ppp_facebook_link', true );
	if ( empty( $current_link ) ) {
		$current_link = '';
	}
	$current_post_id = get_post_meta( $post->ID, '_ppp_facebook_link_post_id', true );
	?>
	<p id="ppp-facebook-post-link" <?php echo !empty( $current_link ) ? 'style="display: none;"' : ''; ?>>
	<?php
	$args = array(
		'name'     => '_ppp_facebook_link_post_id',
		'id'       => 'ppp_facebook_link',
		'chosen'   => true,
		'selected' => false !== $current_post_id ? $current_post_id : 0
		);
	echo ppp_facebook_post_dropdown( $args );
	?>
	</p>
	<span id="ppp-facebook-ext-notice" <?php echo !empty( $current_link ) ? 'style="display: none;"' : ''; ?>>Or <a href="#" id="ppp-facebook-link-to-post"><?php _e( 'Link to an external URL', 'ppp-facebook-txt' ); ?></a></span>
	<p id="ppp-facebook-ext-link" <?php echo empty( $current_link ) ? 'style="display: none;"' : ''; ?>>
		<input display="none" id="ppp-facebook-ext-link-input" type="text" size="50" placeholder="Insert Link URL" name="_ppp_facebook_link" value="<?php echo $current_link; ?>" />&nbsp;<a class="button secondary" href="#" id="ppp-facebook-cancel-ext">Cancel</a><br />
	</p>
	<?php
}

/**
 * Displays the side bar item of 'Facebook Info'
 * @return void
 */
function ppp_facebook_info_callback() {
	global $post;
	$is_cropped = ppp_facebook_maybe_crop_image( $post->ID );
	?>
	<p>
		<label><?php _e( 'Length ', 'ppp-facebook-txt' ); ?>:</label>&nbsp;<span class="ppp-text-length" id="ppp-facebook-details">0</span>
	</p>
	<p>
		<label><?php _e( 'Crop Image', 'ppp-facebook-txt' ); ?>:</label>&nbsp;<input type="checkbox" name="_ppp_facebook_crop_image" value="1" <?php echo checked( '1', $is_cropped, false ); ?> /><br />
		<small><?php _e( 'When checked, will crop to the optimial Facebook image dimensions', 'ppp-facebook-txt' ); ?></small>
	</p>
	<?php
}
