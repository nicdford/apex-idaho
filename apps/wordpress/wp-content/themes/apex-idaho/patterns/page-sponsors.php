<?php
/**
 * Title: Page: Sponsors
 * Slug: apex-idaho/page-sponsors
 * Categories: apex-pages
 * Keywords: sponsors, partners
 * Block Types: core/post-content
 * Post Types: page, wp_template
 * Viewport Width: 1440
 * Description: Title band, every sponsor logo with names, and a become-a-sponsor band.
 *
 * @package apex-idaho
 */

echo apex_idaho_page_band_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'Our partners',
	'Spon<strong>sors</strong>',
	"APEX Idaho wouldn't be possible without the support of these incredible partners."
);
?>

<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|80"}}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:apex/sponsors {"layout":"grid","showNames":true,"align":"wide"} /--></section>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"apex-idaho/cta-sponsor"} /-->
