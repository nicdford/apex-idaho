<?php
/**
 * Title: Stat strip
 * Slug: apex-idaho/stats-strip
 * Categories: apex-sections, apex-events
 * Keywords: stats, numbers, facts, highlights
 * Viewport Width: 1440
 * Description: Four big red facts with labels, divided by hairlines.
 *
 * @package apex-idaho
 */

$apex_stats = array(
	array( '3 days', 'On track' ),
	array( '2', 'Driver parties' ),
	array( 'Free', 'Camping all weekend' ),
	array( 'All', 'Skill levels welcome' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}},"backgroundColor":"asphalt","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-asphalt-background-color has-background" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)"><!-- wp:columns {"align":"wide","className":"apex-stats","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide apex-stats">
<?php foreach ( $apex_stats as $apex_stat ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:paragraph {"className":"apex-stat__num"} -->
<p class="apex-stat__num"><?php echo esc_html( $apex_stat[0] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"apex-stat__label"} -->
<p class="apex-stat__label"><?php echo esc_html( $apex_stat[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
