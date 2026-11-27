<?php
/**
 * Title: Events: Upcoming (cards)
 * Slug: apex-idaho/events-upcoming
 * Categories: apex-events
 * Keywords: events, calendar, schedule, tickets
 * Viewport Width: 1440
 * Description: Section heading with a link to the full calendar, then the next three events as cards. Events come from The Events Calendar.
 *
 * @package apex-idaho
 */
?>
<!-- wp:group {"tagName":"section","align":"full","className":"apex-texture-speed","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull apex-texture-speed has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
<div class="wp-block-group alignwide"><!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">On the calendar</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Upcoming <strong>events</strong></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">Full calendar</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:apex/upcoming-events {"align":"wide"} /--></section>
<!-- /wp:group -->
