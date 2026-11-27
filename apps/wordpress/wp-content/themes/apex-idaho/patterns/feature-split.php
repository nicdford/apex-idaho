<?php
/**
 * Title: Feature: Photo and copy (split)
 * Slug: apex-idaho/feature-split
 * Categories: apex-sections
 * Keywords: feature, image, text, split, about
 * Viewport Width: 1440
 * Description: Angled photo beside an eyebrow, headline, copy, diamond list and button.
 *
 * @package apex-idaho
 */
?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|70"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center"} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"large","linkDestination":"none","className":"is-style-apex-chamfer"} -->
<figure class="wp-block-image size-large is-style-apex-chamfer"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/hero-drift.jpg' ) ); ?>" alt="Two drift cars in tandem" style="aspect-ratio:4/3;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
<div class="wp-block-column is-vertically-aligned-center"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Who we are</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Grassroots, <strong>full send.</strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"smoke"} -->
<p class="has-smoke-color has-text-color">Tell the story here: who runs the events, where they happen and why drivers keep coming back.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-apex-diamonds"} -->
<ul class="wp-block-list is-style-apex-diamonds"><!-- wp:list-item -->
<li>Drift and grip events across Idaho</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Every skill level welcome on track</li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li>Spectators, media and sponsors part of the crew</li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">See upcoming events</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
