<?php
/**
 * Page 2749 — Luau Party landing page.
 *
 * WordPress' template hierarchy picks page-{ID}.php ahead of page.php, so this
 * file overrides page 2749 automatically with no admin step and nothing to
 * switch off by accident. The "Luau Party" page template stays selectable in
 * Page Attributes for any other page that wants the same layout.
 */

$apex_luau_template = locate_template( 'template-luau-party.php' );

if ( $apex_luau_template ) {
	require $apex_luau_template;
} else {
	// Template missing (renamed or not deployed) — fall back to the normal page
	// render rather than fataling on the live site.
	require get_template_directory() . '/page.php';
}
