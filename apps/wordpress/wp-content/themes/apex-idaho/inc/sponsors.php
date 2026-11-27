<?php
/**
 * Sponsor post type and its Carbon Fields meta.
 *
 * Ported unchanged from bb-theme-child: same post type key, same field names,
 * so existing sponsors and their logos carry straight over.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action( 'init', 'apex_idaho_register_sponsor_post_type' );
function apex_idaho_register_sponsor_post_type() {
	register_post_type(
		'sponsor',
		array(
			'labels'       => array(
				'name'          => 'Sponsors',
				'singular_name' => 'Sponsor',
				'add_new_item'  => 'Add New Sponsor',
				'edit_item'     => 'Edit Sponsor',
				'all_items'     => 'All Sponsors',
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => false,
			'supports'     => array( 'title', 'page-attributes' ),
			'menu_icon'    => 'dashicons-star-filled',
		)
	);
}

add_action( 'after_setup_theme', 'apex_idaho_boot_carbon_fields' );
function apex_idaho_boot_carbon_fields() {
	if ( class_exists( '\Carbon_Fields\Carbon_Fields' ) ) {
		\Carbon_Fields\Carbon_Fields::boot();
	}
}

add_action( 'carbon_fields_register_fields', 'apex_idaho_register_sponsor_fields' );
function apex_idaho_register_sponsor_fields() {
	Container::make( 'post_meta', 'Sponsor Details' )
		->where( 'post_type', '=', 'sponsor' )
		->add_fields(
			array(
				Field::make( 'image', 'sponsor_logo', 'Logo' ),
				Field::make( 'select', 'sponsor_to_black', 'Convert to black' )
					->add_options(
						array(
							''           => '— None —',
							'grayscale'  => 'Grayscale',
							'brightness' => 'Brightness',
						)
					),
				Field::make( 'checkbox', 'sponsor_invert', 'Invert colors' )
					->set_help_text( 'Inverts all colors (invert(1)).' ),
				Field::make( 'text', 'sponsor_url', 'Website URL' )
					->set_attribute( 'placeholder', 'https://' ),
			)
		);
}

add_action( 'add_meta_boxes', 'apex_idaho_sponsor_preview_meta_box' );
function apex_idaho_sponsor_preview_meta_box() {
	add_meta_box(
		'sponsor_logo_preview',
		'Logo Preview',
		'apex_idaho_render_sponsor_preview',
		'sponsor',
		'side',
		'high'
	);
}

function apex_idaho_render_sponsor_preview( $post ) {
	$sponsor = apex_idaho_sponsor_from_post( $post->ID, 'medium' );

	if ( ! $sponsor ) {
		echo '<p style="color:#999;margin:0;">No logo uploaded yet.</p>';
		return;
	}

	// Same white board the logos sit on across the site.
	echo '<div style="background:#fff;padding:1rem;text-align:center;border:1px solid #ddd;">';
	echo '<img src="' . esc_url( $sponsor['logo'] ) . '" alt="" style="max-width:100%;max-height:80px;' . ( $sponsor['filter'] ? 'filter:' . esc_attr( $sponsor['filter'] ) . ';' : '' ) . '">';
	echo '</div>';
	echo '<p style="color:#999;font-size:11px;margin:6px 0 0;">Save to refresh preview.</p>';
}

/**
 * One sponsor, normalised.
 *
 * @param int    $post_id Sponsor post ID.
 * @param string $size    Image size.
 * @return array|null Null when the sponsor has no logo (it is skipped everywhere).
 */
function apex_idaho_sponsor_from_post( $post_id, $size = 'medium' ) {
	$logo_id  = apex_idaho_cf_meta( $post_id, 'sponsor_logo' );
	$logo_url = $logo_id ? wp_get_attachment_image_url( (int) $logo_id, $size ) : '';
	if ( ! $logo_url ) {
		return null;
	}

	$to_black = apex_idaho_cf_meta( $post_id, 'sponsor_to_black' );
	$invert   = (bool) apex_idaho_cf_meta( $post_id, 'sponsor_invert' );

	$filters = array_filter(
		array(
			'grayscale' === $to_black ? 'grayscale(1)' : '',
			'brightness' === $to_black ? 'brightness(0)' : '',
			$invert ? 'invert(1)' : '',
		)
	);

	return array(
		'id'     => (int) $post_id,
		'title'  => get_the_title( $post_id ),
		'logo'   => $logo_url,
		'url'    => (string) apex_idaho_cf_meta( $post_id, 'sponsor_url' ),
		'filter' => implode( ' ', $filters ),
		'invert' => $invert,
	);
}

/**
 * All published sponsors with a logo, in menu order.
 *
 * @return array<int,array<string,mixed>>
 */
function apex_idaho_get_sponsors() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}

	$ids = get_posts(
		array(
			'post_type'      => 'sponsor',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	$cache = array();
	foreach ( $ids as $id ) {
		$sponsor = apex_idaho_sponsor_from_post( $id );
		if ( $sponsor ) {
			$cache[] = $sponsor;
		}
	}

	return $cache;
}
