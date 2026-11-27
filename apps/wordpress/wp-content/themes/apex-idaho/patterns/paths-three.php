<?php
/**
 * Title: Pick your line (Drive / Watch / Shoot)
 * Slug: apex-idaho/paths-three
 * Categories: apex-sections
 * Keywords: cards, columns, paths, drivers, spectators, media
 * Viewport Width: 1440
 * Description: Three numbered cards routing drivers, spectators and media to the right place. Each whole card is clickable.
 *
 * @package apex-idaho
 */

$apex_cards = array(
	array( '01', 'Drive', 'Grab a driver entry, pass tech and get on track. Paved pits, camping and driver parties come with our multi-day events.', 'Driver entry', '/events/' ),
	array( '02', 'Watch', 'Spectator tickets put you trackside for tandems, comp runs and the paddock. Bring the crew — it is a weekend, not an afternoon.', 'Spectator tickets', '/events/' ),
	array( '03', 'Shoot', 'Photographers and videographers: apply for media credentials and shoot from the inside. Pink vest, trackside access.', 'Media program', '/media/' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"asphalt","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-asphalt-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Get involved</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Pick your <strong>line.</strong></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
<div class="wp-block-columns alignwide">
<?php foreach ( $apex_cards as $apex_card ) : ?>
<!-- wp:column -->
<div class="wp-block-column"><!-- wp:group {"className":"apex-card apex-path is-style-section-panel","style":{"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"dimensions":{"minHeight":"100%"}},"backgroundColor":"carbon","layout":{"type":"flex","orientation":"vertical","justifyContent":"stretch"}} -->
<div class="wp-block-group apex-card apex-path is-style-section-panel has-carbon-background-color has-background" style="min-height:100%;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"className":"apex-index"} -->
<p class="apex-index"><?php echo esc_html( $apex_card[0] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"huge"} -->
<h3 class="wp-block-heading has-huge-font-size"><?php echo esc_html( $apex_card[1] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"smoke"} -->
<p class="has-smoke-color has-text-color"><?php echo esc_html( $apex_card[2] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"apex-card__link apex-link-arrow","style":{"spacing":{"margin":{"top":"auto"}}}} -->
<p class="apex-card__link apex-link-arrow" style="margin-top:auto"><a href="<?php echo esc_url( home_url( $apex_card[4] ) ); ?>"><?php echo esc_html( $apex_card[3] ); ?></a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
