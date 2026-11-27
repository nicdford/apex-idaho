<?php
/**
 * Title: Hero: Page title band
 * Slug: apex-idaho/hero-page-band
 * Categories: apex-hero
 * Keywords: hero, header, title, band, page
 * Viewport Width: 1440
 * Description: Dark title band with eyebrow, headline, intro and buttons, angled into the next section. Use with the Full-Width Canvas template.
 *
 * @package apex-idaho
 */

echo apex_idaho_page_band_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'APEX Idaho',
	'Page title <strong>goes here</strong>',
	'One or two sentences that set up the page. Keep it short; the sections below do the talking.',
	array(
		array( 'Primary action', home_url( '/events/' ) ),
		array( 'Secondary', home_url( '/about/' ), 'outline' ),
	)
);
