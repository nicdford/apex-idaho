<?php
/**
 * Plugin Name: APEX Idaho Headless API
 * Description: Cache revalidation and the sponsors endpoint for the Next.js front end (apps/web). Theme-independent: works with the block theme or the Beaver Builder child theme active.
 * Version: 1.1.0
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
 * What WordPress manages for the front end: events (The Events Calendar,
 * /wp-json/tribe/events/v1), tickets (Event Tickets, /wp-json/tribe/tickets/v1,
 * sold through WooCommerce on the event page), the shop, news posts
 * (/wp-json/wp/v2/posts) and sponsors (below, shared with the footer of the
 * pages WordPress still serves). Pages are static content in the app.
 *
 * Endpoint (public, read-only):
 *
 *   GET /wp-json/apex/v1/sponsors   sponsor logos with their colour treatment
 *
 * @package apex-headless
 */

defined( 'ABSPATH' ) || exit;

define( 'APEX_HEADLESS_VERSION', '1.1.0' );

/* ==========================================================================
   REST endpoints
   ========================================================================== */

add_action( 'rest_api_init', 'apex_headless_register_routes' );
function apex_headless_register_routes() {
	register_rest_route(
		'apex/v1',
		'/sponsors',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'apex_headless_rest_sponsors',
			'permission_callback' => '__return_true',
		)
	);
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
			'post'               => array( 'posts' ),
			'tribe_events'       => array( 'events' ),
			'tribe_venue'        => array( 'events' ),
			'tribe_organizer'    => array( 'events' ),
			// Tickets: WooCommerce products (Event Tickets' "woo" provider),
			// RSVPs and Tickets Commerce. Event pages show their prices and
			// what is left.
			'product'            => array( 'events' ),
			'tribe_rsvp_tickets' => array( 'events' ),
			'tec_tc_ticket'      => array( 'events' ),
			'sponsor'            => array( 'sponsors' ),
		)
	);
	return isset( $map[ $post_type ] ) ? $map[ $post_type ] : array();
}

add_action( 'save_post', 'apex_headless_queue_revalidation', 20, 2 );
add_action( 'before_delete_post', 'apex_headless_queue_revalidation_on_delete', 20, 2 );
add_action( 'trashed_post', 'apex_headless_queue_revalidation_on_trash', 20 );
// A sale changes how many tickets are left.
add_action( 'woocommerce_product_set_stock', 'apex_headless_queue_events_revalidation' );
add_action( 'event_tickets_after_save_ticket', 'apex_headless_queue_events_revalidation' );

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

function apex_headless_queue_events_revalidation() {
	apex_headless_queue_tags( array( 'events' ) );
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
 * Keep in step with WORDPRESS_PATHS in apps/web/next.config.ts: a path the
 * app sends here but that is missing below would redirect back and forth.
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
			'/mfos-drivers-list',
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
