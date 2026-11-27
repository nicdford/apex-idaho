<?php
/**
 * Title: Header
 * Slug: apex-idaho/header
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 * Description: Ticker strip with upcoming events, then logo, navigation, cart and ticket button.
 *
 * @package apex-idaho
 */

$apex_link = static function ( $path ) {
	return esc_url( home_url( $path ) );
};
?>
<!-- wp:group {"className":"apex-header","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group apex-header">

<!-- wp:group {"align":"full","className":"apex-header__ticker is-style-apex-ticker","style":{"spacing":{"padding":{"top":"0.45rem","bottom":"0.45rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"1.5rem"}},"backgroundColor":"red","textColor":"white","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignfull apex-header__ticker is-style-apex-ticker has-white-color has-red-background-color has-text-color has-background" style="padding-top:0.45rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:0.45rem;padding-left:var(--wp--preset--spacing--40)">
<!-- wp:apex/upcoming-events {"count":4,"layout":"ticker","emptyMessage":"Next season's schedule drops soon"} /-->

<!-- wp:paragraph -->
<p><a href="<?php echo $apex_link( '/kbd-comp/' ); ?>">KBD Comp Series standings</a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Drift · Grip · All skill levels welcome</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"apex-header__bar","style":{"spacing":{"padding":{"top":"0.7rem","bottom":"0.7rem","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|40"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignfull apex-header__bar" style="padding-top:0.7rem;padding-right:var(--wp--preset--spacing--40);padding-bottom:0.7rem;padding-left:var(--wp--preset--spacing--40)">
<!-- wp:site-logo {"width":172,"shouldSyncIcon":false} /-->

<!-- wp:group {"className":"apex-header__right","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
<div class="wp-block-group apex-header__right">
<!-- wp:navigation {"overlayBackgroundColor":"carbon","overlayTextColor":"chalk","className":"apex-nav","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","justifyContent":"right","flexWrap":"nowrap"}} -->
<!-- wp:navigation-link {"label":"Events","url":"<?php echo $apex_link( '/events/' ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Sponsors","url":"<?php echo $apex_link( '/sponsors/' ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Media","url":"<?php echo $apex_link( '/media/' ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Shop","url":"<?php echo $apex_link( '/shop/' ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"About","url":"<?php echo $apex_link( '/about/' ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- wp:navigation-link {"label":"Tech & Rules","url":"<?php echo $apex_link( '/tech-rules/' ); ?>","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation -->

<!-- wp:apex/cart-link /-->

<!-- wp:buttons {"className":"apex-header__cta"} -->
<div class="wp-block-buttons apex-header__cta"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo $apex_link( '/events/' ); ?>">Get Tickets</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

</div>
<!-- /wp:group -->
