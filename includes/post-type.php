<?php

if( !defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the ppp_facebook custom post type
 *
 * @since 1.0
 * @return void
 */
function ppp_facebook_post_type() {

	$posts_labels =  apply_filters( 'ppp_facebook_labels', array(
			'name'               => 'Facebook Posts',
			'singular_name'      => 'Facebook Post',
			'add_new'            => __( 'Add Post', 'ppp-facebook-txt' ),
			'add_new_item'       => __( 'Add Post', 'ppp-facebook-txt' ),
			'edit_item'          => __( 'Edit Post', 'ppp-facebook-txt' ),
			'new_item'           => __( 'New Post', 'ppp-facebook-txt' ),
			'all_items'          => __( 'All Posts', 'ppp-facebook-txt' ),
			'view_item'          => __( 'View Post', 'ppp-facebook-txt' ),
			'search_items'       => __( 'Search Post', 'ppp-facebook-txt' ),
			'not_found'          => __( 'No Posts found', 'ppp-facebook-txt' ),
			'not_found_in_trash' => __( 'No Posts found in Trash', 'ppp-facebook-txt' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Posts', 'ppp-facebook-txt' )
		) );

	$posts_args = array(
		'labels'              => $posts_labels,
		'public'              => false,
		'publicly_queryable'  => false,
		'show_ui'             => true,
		'menu_position'       => 20,
		'menu_icon'           => 'dashicons-facebook',
		'show_in_menu'        => true,
		'query_var'           => true,
		'map_meta_cap'        => true,
		'has_archive'         => false,
		'hierarchical'        => false,
		'exclude_from_search' => true, // changed to true to avoid FB posts from showing up in chosen menu
		'supports'            => apply_filters( 'ppp_facebook_supports', array( 'title', 'thumbnail' ) )
	);
	register_post_type( 'ppp_facebook', apply_filters( 'ppp_facebook_post_args', $posts_args  ) );

}
add_action( 'init', 'ppp_facebook_post_type' );
