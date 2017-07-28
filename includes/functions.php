<?php
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * Generate a dropdown of posts
 * @param  array  $args Array of Arguements
 * @return string       Output of HTML
 */
function ppp_facebook_post_dropdown( $args = array() ) {

	$defaults = array(
		'name'        => 'posts',
		'id'          => 'ppp-posts-dropdown',
		'class'       => '',
		'multiple'    => false,
		'selected'    => 0,
		'chosen'      => true,
		'placeholder' => __( 'Select a Post', 'ppp-facebook-txt' ),
		'number'      => 30
	);

	$args = wp_parse_args( $args, $defaults );

	$posts = get_posts( array(
		'post_type'      => 'any',
		'posts_per_page' => $args['number']
	) );

	$options = array();

	if ( $posts ) {
		$options[0] = __( 'Select a post', 'ppp-facebook-txt' );
		foreach ( $posts as $post ) {
			$options[ absint( $post->ID ) ] = esc_html( $post->post_title );
		}
	} else {
		$options[0] = __( 'No posts found', 'ppp-facebook-txt' );
	}

	// This ensures that any selected products are included in the drop down
	if( is_array( $args['selected'] ) ) {
		foreach( $args['selected'] as $item ) {
			if( ! in_array( $item, $options ) ) {
				$options[$item] = get_the_title( $item );
			}
		}
	} elseif ( is_numeric( $args['selected'] ) && $args['selected'] !== 0 ) {
		if ( ! in_array( $args['selected'], $options ) ) {
			$options[$args['selected']] = get_the_title( $args['selected'] );
		}
	}

	$output = ppp_facebook_render_select( array(
		'name'             => $args['name'],
		'selected'         => $args['selected'],
		'id'               => $args['id'],
		'class'            => $args['class'],
		'options'          => $options,
		'chosen'           => $args['chosen'],
		'multiple'         => $args['multiple'],
		'placeholder'      => $args['placeholder'],
		'show_option_all'  => false,
		'show_option_none' => false
	) );

	return $output;
}

/**
 * Renders a select box
 * @param  array  $args Array of arguements
 * @return string       HTML Output
 */
function ppp_facebook_render_select( $args = array() ) {

	$defaults = array(
		'options'          => array(),
		'name'             => null,
		'class'            => '',
		'id'               => '',
		'selected'         => 0,
		'chosen'           => false,
		'placeholder'      => null,
		'multiple'         => false,
		'show_option_all'  => _x( 'All', 'all dropdown items', 'ppp-facebook-txt' ),
		'show_option_none' => _x( 'None', 'no dropdown items', 'ppp-facebook-txt' )
	);

	$args = wp_parse_args( $args, $defaults );

	if( $args['multiple'] ) {
		$multiple = ' MULTIPLE';
	} else {
		$multiple = '';
	}

	if( $args['chosen'] ) {
		$args['class'] .= ' ppp-facebook-select-chosen';
	}

	if( $args['placeholder'] ) {
		$placeholder = $args['placeholder'];
	} else {
		$placeholder = '';
	}

	$output = '<select name="' . esc_attr( $args[ 'name' ] ) . '" id="' . esc_attr( sanitize_key( str_replace( '-', '_', $args[ 'id' ] ) ) ) . '" class="ppp-facebook-select ' . esc_attr( $args[ 'class'] ) . '"' . $multiple . ' data-placeholder="' . $placeholder . '">';

	if ( ! empty( $args[ 'options' ] ) ) {
		if ( $args[ 'show_option_all' ] ) {
			if( $args['multiple'] ) {
				$selected = selected( true, in_array( 0, $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], 0, false );
			}
			$output .= '<option value="all"' . $selected . '>' . esc_html( $args[ 'show_option_all' ] ) . '</option>';
		}

		if ( $args[ 'show_option_none' ] ) {
			if( $args['multiple'] ) {
				$selected = selected( true, in_array( -1, $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], -1, false );
			}
			$output .= '<option value="-1"' . $selected . '>' . esc_html( $args[ 'show_option_none' ] ) . '</option>';
		}

		foreach( $args[ 'options' ] as $key => $option ) {

			if( $args['multiple'] && is_array( $args['selected'] ) ) {
				$selected = selected( true, in_array( $key, $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], $key, false );
			}

			$output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>';
		}
	}

	$output .= '</select>';

	return $output;
}

/**
 * Determins the crop image setting for a post id
 * @param  int $post_id The post ID
 * @return bool         If the image should be cropped
 */
function ppp_facebook_maybe_crop_image( $post_id ) {
	$should_crop = get_post_meta( $post_id, '_ppp_facebook_crop_image', true );

	$return = empty( $should_crop ) ? false : true;

	return apply_filters( 'ppp_facebook_maybe_crop', $return, $post_id );
}

/**
 * If the ppp_facebook post given should include media, and if it should be cropped or full size
 * @param  int $post_id The Post ID
 * @return mixed        Boolean false if no image should be used, string of a link to the imgae if one is to be used
 */
function ppp_facebook_use_media( $post_id ) {

	if ( !has_post_thumbnail( $post_id ) ) {
		return false;
	}

	$should_crop = isset( $_POST['_ppp_facebook_crop_image'] ) ? $_POST['_ppp_facebook_crop_image'] : ppp_facebook_maybe_crop_image( $post_id );
	if ( !empty( $should_crop ) ) {
		$media = ppp_post_has_media( $post_id, 'fb', true );
		$media = strpos( $media, 'wp-includes/images/media/default.png' ) ? false : $media;
	} else {
		$thumb_id  = get_post_thumbnail_id( $post_id );
		$media     = wp_get_attachment_image_src( $thumb_id, 'full', true );
		$media     = $media[0];
	}

	return $media;
}

function ppp_facebook_schedule() {
	$args  = array( 'm' => date( 'Ym' ), 'post_type' => 'ppp_facebook' );
	$posts = new WP_Query( $args );
	if ( $posts->have_posts() ) {
		while ( $posts->have_posts() ) {
			$posts->the_post();
			?>
			{
				id:    "<?php echo get_the_ID(); ?>",
				title: "<?php echo get_the_title(); ?>",
				start: "<?php echo date_i18n( 'Y-m-d/TH:i:s', strtotime( get_the_date() . ' ' . get_the_time() ) ); ?>",
				className: "ppp-calendar-item-fb",
			},
			<?php
			wp_reset_postdata();
		}
	}
}
add_action( 'ppp_insert_schedule_events', 'ppp_facebook_schedule' );
