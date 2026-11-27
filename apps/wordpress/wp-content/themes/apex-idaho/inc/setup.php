<?php
/**
 * Theme supports, pattern categories, block styles and block bindings.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'apex_idaho_setup' );
function apex_idaho_setup() {
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_editor_style( array( 'assets/css/theme.css', 'assets/css/editor.css' ) );

	// The theme ships its own pattern library; core's generic patterns are noise
	// next to it in the inserter.
	remove_theme_support( 'core-block-patterns' );
}

add_action( 'init', 'apex_idaho_register_pattern_categories' );
function apex_idaho_register_pattern_categories() {
	$categories = array(
		'apex-hero'     => __( 'APEX: Heroes', 'apex-idaho' ),
		'apex-sections' => __( 'APEX: Sections', 'apex-idaho' ),
		'apex-events'   => __( 'APEX: Events', 'apex-idaho' ),
		'apex-cta'      => __( 'APEX: Calls to Action', 'apex-idaho' ),
		'apex-pages'    => __( 'APEX: Full Pages', 'apex-idaho' ),
	);

	foreach ( $categories as $slug => $label ) {
		register_block_pattern_category( $slug, array( 'label' => $label ) );
	}
}

/**
 * Block style variations. Their CSS lives in assets/css/theme.css; section
 * styles for groups (Paper, Hot, Asphalt, Panel) come from styles/sections/.
 */
add_action( 'init', 'apex_idaho_register_block_styles' );
function apex_idaho_register_block_styles() {
	$styles = array(
		'core/paragraph' => array(
			'apex-eyebrow' => __( 'Eyebrow', 'apex-idaho' ),
			'apex-lede'    => __( 'Lede', 'apex-idaho' ),
		),
		'core/heading'   => array(
			'apex-outline'  => __( 'Outline', 'apex-idaho' ),
			'apex-accent'   => __( 'Red', 'apex-idaho' ),
		),
		'core/button'    => array(
			'apex-arrow' => __( 'Arrow', 'apex-idaho' ),
		),
		'core/separator' => array(
			'apex-stripe'   => __( 'Racing Stripe', 'apex-idaho' ),
			'apex-checker'  => __( 'Checkered Flag', 'apex-idaho' ),
			'apex-chevrons' => __( 'Chevrons', 'apex-idaho' ),
		),
		'core/list'      => array(
			'apex-diamonds' => __( 'Diamonds', 'apex-idaho' ),
			'apex-rules'    => __( 'Numbered Rules', 'apex-idaho' ),
		),
		'core/image'     => array(
			'apex-angled'  => __( 'Angled Cut', 'apex-idaho' ),
			'apex-chamfer' => __( 'Chamfer', 'apex-idaho' ),
		),
		'core/table'     => array(
			'apex-timing' => __( 'Timing Sheet', 'apex-idaho' ),
		),
		'core/group'     => array(
			'apex-ticker' => __( 'Ticker (scrolling)', 'apex-idaho' ),
		),
		'core/cover'     => array(
			'apex-speedlines' => __( 'Speed Lines', 'apex-idaho' ),
		),
	);

	foreach ( $styles as $block => $variations ) {
		foreach ( $variations as $name => $label ) {
			register_block_style(
				$block,
				array(
					'name'  => $name,
					'label' => $label,
				)
			);
		}
	}
}

/**
 * Block bindings source for values that must stay live even after a template
 * part has been customised in the Site Editor (patterns are inlined on save,
 * which would freeze a year printed by PHP).
 *
 * Usage on a paragraph:
 * {"metadata":{"bindings":{"content":{"source":"apex-idaho/site","args":{"key":"copyright"}}}}}
 */
add_action( 'init', 'apex_idaho_register_block_bindings' );
function apex_idaho_register_block_bindings() {
	if ( ! function_exists( 'register_block_bindings_source' ) ) {
		return;
	}

	register_block_bindings_source(
		'apex-idaho/site',
		array(
			'label'              => __( 'APEX Idaho site data', 'apex-idaho' ),
			'get_value_callback' => 'apex_idaho_block_binding_value',
		)
	);
}

/**
 * Values are inserted as HTML (core runs them through wp_kses_post), so text
 * is escaped here.
 */
function apex_idaho_block_binding_value( array $args ) {
	$key  = isset( $args['key'] ) ? $args['key'] : '';
	$name = get_bloginfo( 'name' );

	switch ( $key ) {
		case 'year':
			return wp_date( 'Y' );
		case 'copyright':
			/* translators: 1: year, 2: site name. */
			return sprintf( __( '© %1$s %2$s. All rights reserved.', 'apex-idaho' ), wp_date( 'Y' ), esc_html( $name ) );
		case 'site-name':
			return esc_html( $name );
	}

	return null;
}

/**
 * The Site Logo block renders nothing until a Site Logo is set. Activation
 * sets one (below); until then, e.g. in Live Preview, show the bundled logo.
 */
add_filter( 'render_block_core/site-logo', 'apex_idaho_site_logo_fallback', 10, 2 );
function apex_idaho_site_logo_fallback( $content, $block ) {
	if ( '' !== trim( $content ) ) {
		return $content;
	}

	$context = ( isset( $block['attrs']['className'] ) && false !== strpos( $block['attrs']['className'], 'is-footer-logo' ) ) ? 'footer' : 'header';

	return '<div class="wp-block-site-logo apex-site-logo-fallback">' . apex_idaho_logo_fallback_html( $context ) . '</div>';
}

/**
 * Mark the header link for the current section. Navigation links in the
 * header are plain URLs (so the header works before any menu exists), which
 * core cannot match to the queried object; compare paths instead. A link to
 * /events/ is current on /events/ and on anything below it.
 */
add_filter( 'render_block_core/navigation-link', 'apex_idaho_nav_link_current', 10, 2 );
function apex_idaho_nav_link_current( $content, $block ) {
	if ( empty( $block['attrs']['url'] ) || false !== strpos( $content, 'aria-current' ) || ! class_exists( 'WP_HTML_Tag_Processor' ) ) {
		return $content;
	}

	$link_path = wp_parse_url( $block['attrs']['url'], PHP_URL_PATH );
	$link_host = wp_parse_url( $block['attrs']['url'], PHP_URL_HOST );
	if ( ! $link_path || ( $link_host && wp_parse_url( home_url(), PHP_URL_HOST ) !== $link_host ) ) {
		return $content;
	}

	$request_path = wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '', PHP_URL_PATH );
	$link_path    = trailingslashit( $link_path );
	$request_path = trailingslashit( (string) $request_path );

	if ( '/' === $link_path || 0 !== strpos( $request_path, $link_path ) ) {
		return $content;
	}

	$tags = new WP_HTML_Tag_Processor( $content );
	if ( $tags->next_tag( 'a' ) ) {
		$tags->set_attribute( 'aria-current', $request_path === $link_path ? 'page' : 'true' );
		return $tags->get_updated_html();
	}

	return $content;
}

/**
 * On activation, make the APEX Idaho logo the Site Logo if none is set, so the
 * header and footer show it and editors can swap it in the Site Editor.
 *
 * Reuses the logo already in the media library (uploaded for the old theme in
 * 2023/07) when it is there; otherwise imports the copy bundled with the theme.
 */
add_action( 'after_switch_theme', 'apex_idaho_ensure_site_logo' );
function apex_idaho_ensure_site_logo() {
	if ( get_theme_mod( 'custom_logo' ) || get_option( 'site_logo' ) ) {
		return;
	}

	$uploads = wp_get_upload_dir();
	$logo_id = 0;
	foreach ( array( 'APEX_Idaho_logo-2.png', 'APEX_Idaho_logo-2-scaled.png' ) as $file ) {
		$logo_id = attachment_url_to_postid( $uploads['baseurl'] . '/2023/07/' . $file );
		if ( $logo_id ) {
			break;
		}
	}

	if ( ! $logo_id ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$tmp = wp_tempnam( 'apex-idaho-logo.png' );
		if ( $tmp && copy( APEX_IDAHO_DIR . '/assets/images/apex-idaho-logo.png', $tmp ) ) {
			$id = media_handle_sideload(
				array(
					'name'     => 'apex-idaho-logo.png',
					'tmp_name' => $tmp,
				),
				0,
				'APEX Idaho logo'
			);
			if ( is_wp_error( $id ) ) {
				wp_delete_file( $tmp );
			} else {
				$logo_id = (int) $id;
				update_post_meta( $logo_id, '_wp_attachment_image_alt', 'APEX Idaho' );
			}
		}
	}

	if ( $logo_id ) {
		set_theme_mod( 'custom_logo', $logo_id );
		update_option( 'site_logo', $logo_id );
	}
}

/**
 * Full-page patterns (APEX: Full Pages) bring their own title band. When a
 * page's content has one, drop the generic band the Page template adds, so
 * the page works with the default template and never shows two.
 *
 * The template's band is the only one built around a Post Title block.
 */
add_filter( 'render_block_core/group', 'apex_idaho_skip_duplicate_page_band', 10, 2 );
function apex_idaho_skip_duplicate_page_band( $content, $block ) {
	$class = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	if ( false === strpos( $class, 'apex-page-hero' ) || ! is_singular() || empty( $block['innerBlocks'] ) ) {
		return $content;
	}

	$is_template_band = false;
	foreach ( $block['innerBlocks'] as $inner ) {
		if ( 'core/post-title' === $inner['blockName'] ) {
			$is_template_band = true;
			break;
		}
	}
	if ( ! $is_template_band ) {
		return $content;
	}

	$post = get_queried_object();
	if ( $post instanceof WP_Post && false !== strpos( $post->post_content, 'apex-page-hero' ) ) {
		return '';
	}
	return $content;
}

/**
 * Initials on a photo placeholder (a Paragraph with the class
 * "apex-partner-photo__initials") are decoration: the name is the heading
 * below. Hide them from screen readers.
 */
add_filter( 'render_block_core/paragraph', 'apex_idaho_hide_placeholder_initials', 10, 2 );
function apex_idaho_hide_placeholder_initials( $content, $block ) {
	$class = isset( $block['attrs']['className'] ) ? (string) $block['attrs']['className'] : '';
	if ( false === strpos( $class, 'apex-partner-photo__initials' ) ) {
		return $content;
	}
	return preg_replace( '/^(\s*<p\b)/', '$1 aria-hidden="true"', $content, 1 );
}
