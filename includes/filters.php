<?php

/**
 * Alters the placeholder text on the 'title' field for the ppp_facebook post type
 * @param  string $input The string to translate
 * @return string        The string to display
 */
function ppp_facebook_title_placeholder_text( $input ) {

	global $post_type;
	if( 'ppp_facebook' == $post_type && is_admin() && 'Enter title here' == $input ) {
		return __( 'Enter post text', 'ppp-facebook-txt' );
	}

	if( 'ppp_facebook' == $post_type && is_admin() && 'Set featured image' == $input ) {
		return __( 'Attach an image to this post', 'ppp-facebook-txt' );
	}

	return $input;
}
add_filter( 'gettext','ppp_facebook_title_placeholder_text' );

/**
 * Alters the columsn for the ppp_facebook list table
 * @param  array $columns Array of columns
 * @return array          Array of modified columns
 */
function ppp_facebook_columns( $columns ) {
	unset( $columns['author'] );
	unset( $columns['date'] );
	unset( $columns['likes'] );
	$columns['facebook_status']   = __( 'Status', 'ppp-facebook-txt' );
	$columns['fb_post_link']     = __( 'Link', 'ppp-facebook-txt' );
	$columns['image_attached'] = __( 'Image', 'ppp-facebook-txt' );
	$columns['fb_post_date']     = __( 'Post Date', 'ppp-facebook-txt' );
	return $columns;
}
add_filter( 'manage_ppp_facebook_posts_columns' , 'ppp_facebook_columns' );
