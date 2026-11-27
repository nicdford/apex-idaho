<?php
/**
 * Header for the few hand-built PHP templates (template-luau-party.php).
 *
 * Block templates never load this file. It exists so a classic template that
 * calls get_header() renders inside the same header template part as every
 * other page, instead of WordPress' deprecated theme-compat header.
 *
 * Both template parts are rendered BEFORE wp_head(), the same order core's
 * template-canvas.php uses. In a block theme the script-module import map is
 * printed in wp_head, so blocks that enqueue modules (the Navigation block's
 * mobile menu) must have rendered by then or their imports fail to resolve.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

ob_start();
block_template_part( 'header' );
$apex_header_html = ob_get_clean();

ob_start();
block_template_part( 'footer' );
$GLOBALS['apex_idaho_classic_footer_html'] = ob_get_clean();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'apex-classic-template' ); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#apex-main"><?php esc_html_e( 'Skip to content', 'apex-idaho' ); ?></a>
<div class="wp-site-blocks">
<header class="wp-block-template-part"><?php echo $apex_header_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered block markup. ?></header>
<main id="apex-main" class="apex-classic-main">
