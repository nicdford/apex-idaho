<?php
/**
 * APEX Idaho block theme.
 *
 * Replaces the Beaver Builder child theme (bb-theme-child). Everything the
 * child theme did beyond presentation (sponsor CPT, WooCommerce hooks, the
 * Luau landing page, the media package upload) is carried over in inc/ so the
 * site keeps working the moment this theme is activated.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

define( 'APEX_IDAHO_VERSION', '2.2.1' );
define( 'APEX_IDAHO_DIR', get_template_directory() );
define( 'APEX_IDAHO_URI', get_template_directory_uri() );

// Carbon Fields (sponsor meta). Installed by Composer at deploy time.
if ( file_exists( APEX_IDAHO_DIR . '/vendor/autoload.php' ) ) {
	require_once APEX_IDAHO_DIR . '/vendor/autoload.php';
}

require_once APEX_IDAHO_DIR . '/inc/helpers.php';
require_once APEX_IDAHO_DIR . '/inc/setup.php';
require_once APEX_IDAHO_DIR . '/inc/pattern-helpers.php';
require_once APEX_IDAHO_DIR . '/inc/assets.php';
require_once APEX_IDAHO_DIR . '/inc/blocks.php';
require_once APEX_IDAHO_DIR . '/inc/sponsors.php';
require_once APEX_IDAHO_DIR . '/inc/media-partners.php';
require_once APEX_IDAHO_DIR . '/inc/woocommerce.php';
require_once APEX_IDAHO_DIR . '/inc/luau.php';
require_once APEX_IDAHO_DIR . '/inc/compat.php';
