<?php
/**
 * Title: 404
 * Slug: apex-idaho/hidden-404
 * Inserter: no
 *
 * @package apex-idaho
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"apex-texture-night","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|40"},"dimensions":{"minHeight":"70vh"}},"backgroundColor":"carbon","layout":{"type":"constrained","contentSize":"1320px"}} -->
<section class="wp-block-group alignfull apex-texture-night has-carbon-background-color has-background" style="min-height:70vh;padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)">
<!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Red flag · Error 404</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"fontSize":"mega"} -->
<h1 class="wp-block-heading has-mega-font-size">Off<br><em>track.</em></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-apex-lede"} -->
<p class="is-style-apex-lede">The page you were after went off course. It may have moved, or it never existed. Search below, or get back on the racing line.</p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search the site","width":560,"widthUnit":"px","buttonText":"Search"} /-->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to the start line</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">Upcoming events</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:separator {"className":"is-style-apex-checker"} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-apex-checker"/>
<!-- /wp:separator -->
</section>
<!-- /wp:group -->
