<?php
/**
 * Title: Page: About
 * Slug: apex-idaho/page-about
 * Categories: apex-pages
 * Keywords: about, team, mission, crew, history
 * Block Types: core/post-content
 * Post Types: page, wp_template
 * Viewport Width: 1440
 * Description: About page: title band, mission, what we put on track, the team and a closing call to action.
 *
 * @package apex-idaho
 */

$apex_focus = array( 'Grassroots drift comps', 'Novice driver development', 'Grip / time attack' );

echo apex_idaho_page_band_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'Drifting since 2007',
	'Ab<strong>out</strong>',
	'Developing drivers, cars, tracks, scenes and more.',
	array(),
	'asphalt'
);
?>

<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"asphalt","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-asphalt-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Our skills &amp; expertise</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Built by drivers, for drivers</h2>
<!-- /wp:heading --></div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">APEX Idaho has been drifting since 2007. Developing drivers, cars, tracks, scenes and more. We’re dedicated to designing and hosting premier events that showcase the world of drifting and that deeply engage with and contribute to the local and greater motorsport communities.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Our mission is to elevate the experience of each attendee, participant, and sponsor, by creating unforgettable experiences that resonate with the passion and excitement inherent in motorsports culture.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:column --></div>
<!-- /wp:columns -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
<div class="wp-block-columns alignwide">
<?php foreach ( $apex_focus as $apex_i => $apex_item ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"apex-card apex-path is-style-section-panel","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100%"}},"backgroundColor":"carbon","layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group apex-card apex-path is-style-section-panel has-carbon-background-color has-background" style="min-height:100%;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"className":"apex-index"} -->
<p class="apex-index"><?php echo esc_html( str_pad( (string) ( $apex_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"xx-large"} -->
<h3 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html( $apex_item ); ?></h3>
<!-- /wp:heading --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->

<!-- wp:pattern {"slug":"apex-idaho/team-grid"} /-->

<!-- wp:pattern {"slug":"apex-idaho/cta-line"} /-->
