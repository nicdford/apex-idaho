<?php
/**
 * Theme blocks.
 *
 * All five are dynamic (render.php), so their output always reflects live data
 * — events from The Events Calendar, sponsors from the Sponsor post type — and
 * none of them can go "invalid" in the editor. The editor UI is one plain
 * script with no build step, because the deploy pipeline has no npm stage.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'apex_idaho_register_blocks' );
function apex_idaho_register_blocks() {
	wp_register_script(
		'apex-idaho-blocks-editor',
		APEX_IDAHO_URI . '/blocks/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		apex_idaho_file_version( 'blocks/editor.js' ),
		true
	);

	wp_register_style(
		'apex-idaho-blocks',
		APEX_IDAHO_URI . '/blocks/blocks.css',
		array(),
		apex_idaho_file_version( 'blocks/blocks.css' )
	);

	foreach ( array( 'upcoming-events', 'sponsors', 'countdown', 'partner-photo', 'cart-link' ) as $block ) {
		register_block_type( APEX_IDAHO_DIR . '/blocks/' . $block );
	}
}

add_filter( 'block_categories_all', 'apex_idaho_block_category' );
function apex_idaho_block_category( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'apex-idaho',
			'title' => __( 'APEX Idaho', 'apex-idaho' ),
		)
	);
	return $categories;
}
