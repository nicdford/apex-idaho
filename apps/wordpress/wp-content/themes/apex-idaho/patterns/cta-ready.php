<?php
/**
 * Title: CTA: Ready to race?
 * Slug: apex-idaho/cta-ready
 * Categories: apex-cta, apex-events
 * Keywords: cta, register, tickets, closing
 * Viewport Width: 1440
 * Description: Centered closing call-out with driver and spectator buttons.
 *
 * @package apex-idaho
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"apex-texture-night","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|30"}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull apex-texture-night has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:paragraph {"align":"center","className":"is-style-apex-eyebrow"} -->
<p class="has-text-align-center is-style-apex-eyebrow">Don't miss it</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","fontSize":"display"} -->
<h2 class="wp-block-heading has-text-align-center has-display-font-size">Ready to <strong>race?</strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","className":"is-style-apex-lede"} -->
<p class="has-text-align-center is-style-apex-lede">Spots are limited — lock in your entry before they're gone.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">Buy driver entry</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">Spectator tickets</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
