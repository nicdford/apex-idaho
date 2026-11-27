<?php
/**
 * Title: Page: Media
 * Slug: apex-idaho/page-media
 * Categories: apex-pages
 * Keywords: media, photographers, press, partners
 * Block Types: core/post-content
 * Post Types: page, wp_template
 * Viewport Width: 1440
 * Description: Media program page: title band, media package promo, contact strip, partner cards, rules and apply band.
 *
 * @package apex-idaho
 */

echo apex_idaho_page_band_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'APEX Idaho',
	'Me<strong>dia</strong>',
	'Come capture the magic that is APEX Idaho events.',
	array(
		array( 'Media partners', '#partners' ),
		array( 'Become a media partner', '#become', 'outline' ),
	)
);
?>

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-section-hot","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-section-hot" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|10"},"layout":{"selfStretch":"fixed","flexSize":"min(100%, 680px)"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">For drivers</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"fontSize":"xx-large"} -->
<h2 class="wp-block-heading has-xx-large-font-size">Want pro shots of your car on track?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Book the APEX Idaho Media Package and get your run captured by our team — high-quality photos delivered after the event.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/product/media-package/' ) ); ?>">Book media package</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"apex-idaho/contact-strip"} /-->

<!-- wp:pattern {"slug":"apex-idaho/media-partners"} /-->

<!-- wp:pattern {"slug":"apex-idaho/media-rules"} /-->

<!-- wp:group {"tagName":"section","align":"full","className":"apex-texture-night apex-stripe-top","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|30"}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull apex-texture-night apex-stripe-top has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:heading {"textAlign":"center","fontSize":"display"} -->
<h2 class="wp-block-heading has-text-align-center has-display-font-size">Ready to <strong>apply?</strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"is-style-apex-lede"} -->
<p class="has-text-align-center is-style-apex-lede">Fill out the media application, we'll get back to you, and you'll be ready to capture photos and video at APEX Idaho events.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/media/media-application/' ) ); ?>">Apply now</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
