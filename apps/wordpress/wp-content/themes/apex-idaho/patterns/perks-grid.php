<?php
/**
 * Title: Perks grid (numbered)
 * Slug: apex-idaho/perks-grid
 * Categories: apex-sections, apex-events
 * Keywords: perks, features, included, grid, numbered
 * Viewport Width: 1440
 * Description: Section heading and six numbered tiles, e.g. what a driver entry includes.
 *
 * @package apex-idaho
 */

$apex_perks = array(
	array( '3-Day Track Access', 'Full access to on-track sessions across all three event days.' ),
	array( 'Paved Pits', 'Dedicated paved pit space with access to all track amenities.' ),
	array( 'Driver Parties', 'Exclusive Friday and Saturday night driver parties included.' ),
	array( 'Free Camping', 'Camp on-site all weekend — no reservation needed.' ),
	array( 'Showers On-Site', 'Clean shower facilities available throughout the event.' ),
	array( 'Fuel Available', 'On-site fuel, so you spend less time sourcing and more time driving.' ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Driver entry includes</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Everything for <strong>three days</strong> on track</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"grid","minimumColumnWidth":"19rem"}} -->
<div class="wp-block-group alignwide">
<?php foreach ( $apex_perks as $apex_i => $apex_perk ) : ?>
<!-- wp:group {"className":"apex-perk apex-card is-style-section-panel","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group apex-perk apex-card is-style-section-panel"><!-- wp:paragraph {"className":"apex-index"} -->
<p class="apex-index"><?php echo esc_html( str_pad( (string) ( $apex_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"x-large"} -->
<h3 class="wp-block-heading has-x-large-font-size"><?php echo esc_html( $apex_perk[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"smoke"} -->
<p class="has-smoke-color has-text-color"><?php echo esc_html( $apex_perk[1] ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">Register as a driver</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></section>
<!-- /wp:group -->
