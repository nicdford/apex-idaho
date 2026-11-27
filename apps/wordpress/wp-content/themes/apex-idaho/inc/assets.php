<?php
/**
 * Front-end assets.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version for a theme file: its mtime, falling back to the theme
 * version. Deploys mirror files, so mtime changes whenever content does.
 */
function apex_idaho_file_version( $relative ) {
	$path = APEX_IDAHO_DIR . '/' . ltrim( $relative, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : APEX_IDAHO_VERSION;
}

add_action( 'wp_enqueue_scripts', 'apex_idaho_enqueue_assets' );
function apex_idaho_enqueue_assets() {
	wp_enqueue_style(
		'apex-idaho',
		apex_idaho_asset( 'css/theme.css' ),
		array(),
		apex_idaho_file_version( 'assets/css/theme.css' )
	);

	// Third-party UIs (calendar, tickets, shop, legacy Beaver Builder layouts)
	// were all styled for a light page. Only load the overrides where one of
	// them is actually present.
	if ( apex_idaho_needs_compat_css() ) {
		wp_enqueue_style(
			'apex-idaho-compat',
			apex_idaho_asset( 'css/compat.css' ),
			array( 'apex-idaho' ),
			apex_idaho_file_version( 'assets/css/compat.css' )
		);
	}

	wp_enqueue_script(
		'apex-idaho',
		apex_idaho_asset( 'js/theme.js' ),
		array(),
		apex_idaho_file_version( 'assets/js/theme.js' ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}

function apex_idaho_needs_compat_css() {
	return class_exists( 'Tribe__Events__Main' )
		|| class_exists( 'Tribe__Tickets__Main' )
		|| class_exists( 'WooCommerce' )
		|| class_exists( 'FLBuilderModel' );
}

/**
 * Preload the two faces every page paints above the fold: the display italic
 * used by headings and the body regular.
 */
add_action( 'wp_head', 'apex_idaho_preload_fonts', 1 );
function apex_idaho_preload_fonts() {
	$fonts = array(
		'fonts/barlow-condensed-latin-900-italic.woff2',
		'fonts/barlow-latin-400-normal.woff2',
	);
	foreach ( $fonts as $font ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( apex_idaho_asset( $font ) )
		);
	}
}
