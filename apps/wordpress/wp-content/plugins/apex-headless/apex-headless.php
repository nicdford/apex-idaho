<?php
/**
 * Plugin Name: APEX Idaho Headless API
 * Description: Data endpoints and cache revalidation for the Next.js front end (apps/web). Theme-independent: works with the block theme or the Beaver Builder child theme active.
 * Version: 1.0.0
 * Requires at least: 6.6
 * Requires PHP: 7.4
 * Author: Nic D. Ford
 * License: GPL-2.0-or-later
 *
 * Configuration (wp-config.php):
 *
 *   define( 'APEX_HEADLESS_FRONTEND_URL', 'https://apex-idaho.com' ); // the Next.js site
 *   define( 'APEX_HEADLESS_REVALIDATE_SECRET', '…long random string…' ); // same as REVALIDATE_SECRET in the Next.js env
 *   define( 'APEX_HEADLESS_REDIRECT', false ); // true at cutover: send public page views to the Next.js site
 *
 * Endpoints (all public, read-only):
 *
 *   GET /wp-json/apex/v1/site            name, tagline, logo, site icon, social links
 *   GET /wp-json/apex/v1/sponsors        sponsor logos with their colour treatment
 *   GET /wp-json/apex/v1/media-partners  media partner photos with focal points
 *
 * Events come from The Events Calendar's own API (/wp-json/tribe/events/v1),
 * pages and posts from core (/wp-json/wp/v2).
 *
 * @package apex-headless
 */

defined( 'ABSPATH' ) || exit;

define( 'APEX_HEADLESS_VERSION', '1.0.0' );

/* ==========================================================================
   REST endpoints
   ========================================================================== */

add_action( 'rest_api_init', 'apex_headless_register_routes' );
function apex_headless_register_routes() {
	$routes = array(
		'/site'           => 'apex_headless_rest_site',
		'/sponsors'       => 'apex_headless_rest_sponsors',
		'/media-partners' => 'apex_headless_rest_media_partners',
	);

	foreach ( $routes as $route => $callback ) {
		register_rest_route(
			'apex/v1',
			$route,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => $callback,
				'permission_callback' => '__return_true',
			)
		);
	}
}

/**
 * A theme mod from the active theme, falling back to the Beaver Builder
 * themes' saved settings (theme mods are stored per theme).
 */
function apex_headless_theme_mod( $name ) {
	$value = get_theme_mod( $name );
	if ( $value ) {
		return (string) $value;
	}
	foreach ( array( 'bb-theme-child', 'bb-theme', 'apex-idaho' ) as $stylesheet ) {
		$mods = get_option( 'theme_mods_' . $stylesheet );
		if ( is_array( $mods ) && ! empty( $mods[ $name ] ) ) {
			return (string) $mods[ $name ];
		}
	}
	return '';
}

/**
 * Image data for an attachment, or null.
 */
function apex_headless_image( $attachment_id, $size = 'large' ) {
	$attachment_id = (int) $attachment_id;
	if ( ! $attachment_id ) {
		return null;
	}
	$src = wp_get_attachment_image_src( $attachment_id, $size );
	if ( ! $src ) {
		return null;
	}
	return array(
		'url'    => $src[0],
		'width'  => (int) $src[1],
		'height' => (int) $src[2],
		'alt'    => (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ),
	);
}

function apex_headless_rest_site() {
	$logo_id = (int) get_option( 'site_logo' );
	if ( ! $logo_id ) {
		$logo_id = (int) get_theme_mod( 'custom_logo' );
	}
	$logo = apex_headless_image( $logo_id, 'full' );

	// The block theme ships the logo; use it when no Site Logo is set yet.
	if ( ! $logo && file_exists( WP_CONTENT_DIR . '/themes/apex-idaho/assets/images/apex-idaho-logo.png' ) ) {
		$logo = array(
			'url'    => content_url( '/themes/apex-idaho/assets/images/apex-idaho-logo.png' ),
			'width'  => 800,
			'height' => 205,
			'alt'    => get_bloginfo( 'name' ),
		);
	}

	$social = array_filter(
		array(
			'instagram' => apex_headless_theme_mod( 'fl-social-instagram' ),
			'facebook'  => apex_headless_theme_mod( 'fl-social-facebook' ),
			'youtube'   => apex_headless_theme_mod( 'fl-social-youtube' ),
			'tiktok'    => apex_headless_theme_mod( 'fl-social-tiktok' ),
		)
	);

	$data = array(
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'url'         => home_url( '/' ),
		'logo'        => $logo,
		'icon'        => get_site_icon_url( 512 ) ? get_site_icon_url( 512 ) : null,
		'social'      => (object) $social,
	);

	return rest_ensure_response( apply_filters( 'apex_headless_site', $data ) );
}

/**
 * Carbon Fields stores simple fields as `_{name}` post meta; read through the
 * library when it is loaded, else read the meta directly.
 */
function apex_headless_cf_meta( $post_id, $key ) {
	if ( function_exists( 'carbon_get_post_meta' ) ) {
		return carbon_get_post_meta( $post_id, $key );
	}
	return get_post_meta( $post_id, '_' . $key, true );
}

function apex_headless_rest_sponsors() {
	if ( ! post_type_exists( 'sponsor' ) ) {
		return rest_ensure_response( array() );
	}

	$ids = get_posts(
		array(
			'post_type'      => 'sponsor',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	$out = array();
	foreach ( $ids as $id ) {
		$logo = apex_headless_image( apex_headless_cf_meta( $id, 'sponsor_logo' ), 'medium_large' );
		if ( ! $logo ) {
			continue;
		}
		$to_black = (string) apex_headless_cf_meta( $id, 'sponsor_to_black' );
		$invert   = (bool) apex_headless_cf_meta( $id, 'sponsor_invert' );
		$filters  = array_values(
			array_filter(
				array(
					'grayscale' === $to_black ? 'grayscale(1)' : '',
					'brightness' === $to_black ? 'brightness(0)' : '',
					$invert ? 'invert(1)' : '',
				)
			)
		);

		$out[] = array(
			'id'     => (int) $id,
			'name'   => html_entity_decode( get_the_title( $id ), ENT_QUOTES, 'UTF-8' ),
			'url'    => (string) apex_headless_cf_meta( $id, 'sponsor_url' ),
			'logo'   => $logo,
			'filter' => implode( ' ', $filters ),
			'invert' => $invert,
		);
	}

	return rest_ensure_response( $out );
}

function apex_headless_rest_media_partners() {
	if ( ! post_type_exists( 'media_partner' ) ) {
		return rest_ensure_response( array() );
	}

	$posts = get_posts(
		array(
			'post_type'      => 'media_partner',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'  => true,
		)
	);

	$out = array();
	foreach ( $posts as $post ) {
		$x = get_post_meta( $post->ID, '_apex_focal_x', true );
		$y = get_post_meta( $post->ID, '_apex_focal_y', true );

		$out[] = array(
			'id'    => (int) $post->ID,
			'slug'  => $post->post_name,
			'name'  => html_entity_decode( get_the_title( $post ), ENT_QUOTES, 'UTF-8' ),
			'link'  => get_permalink( $post ),
			'photo' => apex_headless_image( get_post_thumbnail_id( $post ), 'medium_large' ),
			'focal' => ( '' === $x ? 50 : (int) $x ) . '% ' . ( '' === $y ? 25 : (int) $y ) . '%',
		);
	}

	return rest_ensure_response( $out );
}

/* ==========================================================================
   Revalidation: tell the Next.js site which cached data changed
   ========================================================================== */

/**
 * Cache tags the front end uses, per post type. Filterable.
 */
function apex_headless_tags_for_post_type( $post_type ) {
	$map = apply_filters(
		'apex_headless_revalidate_map',
		array(
			'post'          => array( 'posts' ),
			'page'          => array( 'pages' ),
			'tribe_events'  => array( 'events' ),
			'tribe_venue'   => array( 'events' ),
			'sponsor'       => array( 'sponsors' ),
			'media_partner' => array( 'partners' ),
			'attachment'    => array(),
		)
	);
	return isset( $map[ $post_type ] ) ? $map[ $post_type ] : array();
}

add_action( 'save_post', 'apex_headless_queue_revalidation', 20, 2 );
add_action( 'before_delete_post', 'apex_headless_queue_revalidation_on_delete', 20, 2 );
add_action( 'trashed_post', 'apex_headless_queue_revalidation_on_trash', 20 );
add_action( 'updated_option', 'apex_headless_queue_site_revalidation', 20 );

function apex_headless_queue_revalidation( $post_id, $post = null ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	$post = $post ? $post : get_post( $post_id );
	if ( ! $post || 'auto-draft' === $post->post_status ) {
		return;
	}
	apex_headless_queue_tags( apex_headless_tags_for_post_type( $post->post_type ) );
}

function apex_headless_queue_revalidation_on_delete( $post_id, $post = null ) {
	apex_headless_queue_revalidation( $post_id, $post instanceof WP_Post ? $post : null );
}

// trashed_post passes the previous status as its second argument, not a post.
function apex_headless_queue_revalidation_on_trash( $post_id ) {
	apex_headless_queue_revalidation( $post_id );
}

function apex_headless_queue_site_revalidation( $option ) {
	if ( in_array( $option, array( 'blogname', 'blogdescription', 'site_logo', 'site_icon' ), true ) ) {
		apex_headless_queue_tags( array( 'site' ) );
	}
}

function apex_headless_queue_tags( $tags ) {
	if ( ! $tags ) {
		return;
	}
	if ( ! isset( $GLOBALS['apex_headless_pending_tags'] ) ) {
		$GLOBALS['apex_headless_pending_tags'] = array();
		add_action( 'shutdown', 'apex_headless_send_revalidation' );
	}
	$GLOBALS['apex_headless_pending_tags'] = array_values( array_unique( array_merge( $GLOBALS['apex_headless_pending_tags'], $tags ) ) );
}

/**
 * One request per page load, sent at shutdown and not waited on, so saving
 * in the editor never stalls on the front end being slow or down.
 */
function apex_headless_send_revalidation() {
	$tags = isset( $GLOBALS['apex_headless_pending_tags'] ) ? $GLOBALS['apex_headless_pending_tags'] : array();
	if ( ! $tags || ! defined( 'APEX_HEADLESS_FRONTEND_URL' ) || ! defined( 'APEX_HEADLESS_REVALIDATE_SECRET' ) ) {
		return;
	}

	wp_remote_post(
		untrailingslashit( APEX_HEADLESS_FRONTEND_URL ) . '/api/revalidate',
		array(
			'blocking' => false,
			'timeout'  => 3,
			'headers'  => array(
				'Content-Type'        => 'application/json',
				'x-revalidate-secret' => APEX_HEADLESS_REVALIDATE_SECRET,
			),
			'body'     => wp_json_encode( array( 'tags' => $tags ) ),
		)
	);
}

/* ==========================================================================
   Optional: send public page views to the Next.js site (cutover)
   ========================================================================== */

/**
 * Paths WordPress keeps serving itself: shopping, checkout, accounts, ticket
 * pages (Event Tickets' purchase form lives on the event page) and forms.
 */
function apex_headless_wordpress_paths() {
	return apply_filters(
		'apex_headless_wordpress_paths',
		array(
			'/cart',
			'/checkout',
			'/my-account',
			'/shop',
			'/product/',
			'/product-category/',
			'/event/',
			'/events/',
			'/tickets-checkout',
			'/tickets-order',
			'/media/media-application',
			'/mfos-driver-registration',
			'/wc-api/',
		)
	);
}

add_action( 'template_redirect', 'apex_headless_maybe_redirect', 1 );
function apex_headless_maybe_redirect() {
	if ( ! defined( 'APEX_HEADLESS_REDIRECT' ) || ! APEX_HEADLESS_REDIRECT || ! defined( 'APEX_HEADLESS_FRONTEND_URL' ) ) {
		return;
	}
	if ( is_admin() || wp_doing_ajax() || is_preview() || is_customize_preview() || is_feed() || is_robots() || is_trackback() ) {
		return;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return;
	}
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['wc-ajax'] ) || isset( $_GET['add-to-cart'] ) ) {
		return;
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );

	foreach ( apex_headless_wordpress_paths() as $prefix ) {
		if ( 0 === strpos( trailingslashit( $path ), trailingslashit( rtrim( $prefix, '/' ) ) ) || 0 === strpos( $path, $prefix ) ) {
			return;
		}
	}

	$target = untrailingslashit( APEX_HEADLESS_FRONTEND_URL ) . $uri;
	if ( wp_parse_url( $target, PHP_URL_HOST ) === wp_parse_url( home_url(), PHP_URL_HOST ) ) {
		return; // Misconfiguration guard: never redirect to ourselves.
	}

	// 302 until the cutover is settled; switch to 301 with the filter.
	wp_redirect( $target, (int) apply_filters( 'apex_headless_redirect_status', 302 ) ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
	exit;
}
