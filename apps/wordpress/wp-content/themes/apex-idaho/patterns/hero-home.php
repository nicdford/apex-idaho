<?php
/**
 * Title: Hero: Full-bleed photo with countdown
 * Slug: apex-idaho/hero-home
 * Categories: apex-hero
 * Keywords: hero, banner, cover, countdown, home
 * Viewport Width: 1440
 * Description: Full-height photo hero with speed lines, headline, two buttons and a live countdown to the next event.
 *
 * @package apex-idaho
 */

$apex_hero = esc_url( get_theme_file_uri( 'assets/images/hero-drift.jpg' ) );
?>
<!-- wp:cover {"url":"<?php echo $apex_hero; ?>","dimRatio":100,"gradient":"hero-fade","focalPoint":{"x":0.55,"y":0.5},"minHeight":86,"minHeightUnit":"vh","contentPosition":"bottom left","align":"full","className":"is-style-apex-speedlines apex-cut-bottom","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"calc(var(--wp--preset--spacing--70) + 3rem)","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"layout":{"type":"constrained","contentSize":"1320px"}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-bottom-left is-style-apex-speedlines apex-cut-bottom" style="padding-top:var(--wp--preset--spacing--80);padding-right:var(--wp--preset--spacing--40);padding-bottom:calc(var(--wp--preset--spacing--70) + 3rem);padding-left:var(--wp--preset--spacing--40);min-height:86vh"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim wp-block-cover__gradient-background has-background-gradient has-hero-fade-gradient-background"></span><img class="wp-block-cover__image-background" alt="" src="<?php echo $apex_hero; ?>" style="object-position:55% 50%" data-object-fit="cover" data-object-position="55% 50%"/><div class="wp-block-cover__inner-container"><!-- wp:columns {"verticalAlignment":"bottom","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-bottom"><!-- wp:column {"verticalAlignment":"bottom","width":"62%","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:62%"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Drift · Grip · Comp Series</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Idaho drives<br><strong>sideways.</strong></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-apex-lede"} -->
<p class="is-style-apex-lede">Drift, grip and full-throttle competition across the Gem State, from Meridian Speedway to Magic Valley. Every skill level on track, spectators welcome, and a paddock that knows how to party.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}}} -->
<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--30)"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">See the schedule</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Shop &amp; tickets</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"bottom","width":"38%"} -->
<div class="wp-block-column is-vertically-aligned-bottom" style="flex-basis:38%"><!-- wp:apex/countdown /--></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
