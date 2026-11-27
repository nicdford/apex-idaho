<?php
/**
 * Compatibility shims: core quirks, plugins, and pages still built in Beaver
 * Builder during the move to blocks.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

/**
 * WP core's iframe_header() dispatches admin_enqueue_scripts with a null hook,
 * which crashes any callback that types its first arg as `string` (seen in
 * Event Tickets' vendored Harbor Feature_Manager_Page). Wrap such callbacks so
 * null becomes "" — keeps third-party plugins working without patching vendor
 * code. Ported unchanged from bb-theme-child.
 */
add_action( 'admin_init', 'apex_idaho_wrap_strict_admin_enqueue_callbacks', 1 );
function apex_idaho_wrap_strict_admin_enqueue_callbacks() {
	global $wp_filter;
	if ( empty( $wp_filter['admin_enqueue_scripts'] ) ) {
		return;
	}
	foreach ( $wp_filter['admin_enqueue_scripts']->callbacks as $priority => $callbacks ) {
		foreach ( $callbacks as $id => $cb ) {
			$fn = $cb['function'];
			try {
				$ref = is_array( $fn )
					? new ReflectionMethod( $fn[0], $fn[1] )
					: ( is_string( $fn ) || $fn instanceof Closure ? new ReflectionFunction( $fn ) : null );
			} catch ( Throwable $e ) {
				continue;
			}
			if ( ! $ref ) {
				continue;
			}
			$params = $ref->getParameters();
			if ( ! $params ) {
				continue;
			}
			$type = $params[0]->getType();
			if ( ! $type instanceof ReflectionNamedType ) {
				continue;
			}
			if ( $type->allowsNull() || 'string' !== $type->getName() ) {
				continue;
			}
			$wp_filter['admin_enqueue_scripts']->callbacks[ $priority ][ $id ]['function'] = function ( $hook = null ) use ( $fn ) {
				return call_user_func( $fn, null === $hook ? '' : $hook );
			};
		}
	}
}

/**
 * Pages still laid out in Beaver Builder were designed for a white page. Flag
 * them so compat.css can give their content a light canvas until each page is
 * rebuilt with blocks (turn the builder off on the page to drop the flag).
 */
add_filter( 'body_class', 'apex_idaho_legacy_builder_body_class' );
function apex_idaho_legacy_builder_body_class( $classes ) {
	if ( is_singular() && class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'is_builder_enabled' ) ) {
		if ( FLBuilderModel::is_builder_enabled( get_queried_object_id() ) ) {
			$classes[] = 'apex-legacy-builder';
		}
	}
	return $classes;
}
