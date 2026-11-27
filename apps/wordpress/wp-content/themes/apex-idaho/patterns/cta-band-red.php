<?php
/**
 * Title: CTA: Red band (Tech & Rules)
 * Slug: apex-idaho/cta-band-red
 * Categories: apex-cta
 * Keywords: cta, banner, band, rules, red
 * Viewport Width: 1440
 * Description: Solid red band with a headline, one line of copy and a dark button. Defaults to the Tech & Rules call-out.
 *
 * @package apex-idaho
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"is-style-section-red apex-texture-speed","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-section-red apex-texture-speed" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"},"layout":{"selfStretch":"fixed","flexSize":"min(100%, 640px)"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:heading {"fontSize":"huge"} -->
<h2 class="wp-block-heading has-huge-font-size">Official tech &amp; rules</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Review the technical requirements and event rules before you register. Every car goes through tech.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/tech-rules/' ) ); ?>">View tech &amp; rules</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
