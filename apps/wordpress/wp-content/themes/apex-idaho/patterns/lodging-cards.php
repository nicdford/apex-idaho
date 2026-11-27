<?php
/**
 * Title: Partner lodging
 * Slug: apex-idaho/lodging-cards
 * Categories: apex-events, apex-sections
 * Keywords: hotels, lodging, stay, accommodation
 * Viewport Width: 1440
 * Description: Two hotel cards with address, amenities and a map link.
 *
 * @package apex-idaho
 */

$apex_hotels = array(
	array( 'Hampton Inn', '1658 Fillmore St., Twin Falls', array( 'Free breakfast included', 'Indoor pool & fitness center', 'Event rate available — ask at booking' ) ),
	array( 'Quality Inn', '1910 Fillmore St., Twin Falls', array( 'Complimentary continental breakfast', 'Outdoor pool', 'Pet-friendly rooms available' ) ),
);
?>
<!-- wp:group {"tagName":"section","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Stay close to the track</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Partner <strong>lodging</strong></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"smoke"} -->
<p class="has-smoke-color has-text-color">Official hotel partners with reserved blocks for event attendees.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
<div class="wp-block-columns alignwide">
<?php foreach ( $apex_hotels as $apex_hotel ) : ?>
<!-- wp:column {"className":"is-style-section-panel","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"blockGap":"var:preset|spacing|30"},"border":{"left":{"color":"var:preset|color|red","width":"5px","style":"solid"}}}} -->
<div class="wp-block-column is-style-section-panel" style="border-left-color:var(--wp--preset--color--red);border-left-style:solid;border-left-width:5px;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)"><!-- wp:heading {"level":3,"fontSize":"xx-large"} -->
<h3 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html( $apex_hotel[0] ); ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"smoke"} -->
<p class="has-smoke-color has-text-color"><?php echo esc_html( $apex_hotel[1] ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"is-style-apex-diamonds"} -->
<ul class="wp-block-list is-style-apex-diamonds"><?php foreach ( $apex_hotel[2] as $apex_amenity ) : ?><!-- wp:list-item -->
<li><?php echo esc_html( $apex_amenity ); ?></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( 'https://maps.google.com/?q=' . rawurlencode( $apex_hotel[0] . ' ' . $apex_hotel[1] . ' ID' ) ); ?>" target="_blank" rel="noreferrer noopener">Map &amp; directions</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
