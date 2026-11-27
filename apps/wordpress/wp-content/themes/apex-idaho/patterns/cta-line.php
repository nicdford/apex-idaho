<?php
/**
 * Title: CTA: See you at the line
 * Slug: apex-idaho/cta-line
 * Categories: apex-cta
 * Keywords: cta, call to action, closing, tickets
 * Viewport Width: 1440
 * Description: Huge centered closing headline with ticket and event buttons on a grid texture.
 *
 * @package apex-idaho
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"apex-texture-night apex-stripe-top","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|40"}},"backgroundColor":"carbon","layout":{"type":"constrained","contentSize":"1320px"}} -->
<section class="wp-block-group alignfull apex-texture-night apex-stripe-top has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:paragraph {"align":"center","className":"is-style-apex-eyebrow"} -->
<p class="has-text-align-center is-style-apex-eyebrow">Your move</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"textAlign":"center","fontSize":"mega"} -->
<h2 class="wp-block-heading has-text-align-center has-mega-font-size">See you at<br><strong>the line.</strong></h2>
<!-- /wp:heading -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">Get tickets</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/tech-rules/' ) ); ?>">Tech &amp; rules</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
