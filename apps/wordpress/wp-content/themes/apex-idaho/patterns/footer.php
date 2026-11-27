<?php
/**
 * Title: Footer
 * Slug: apex-idaho/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 * Description: Sponsor board, link columns, socials, copyright and a checkered flag.
 *
 * @package apex-idaho
 */

$apex_link = static function ( $path ) {
	return esc_url( home_url( $path ) );
};
?>
<!-- wp:group {"className":"apex-footer","style":{"spacing":{"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group apex-footer">

<!-- wp:group {"align":"full","className":"apex-sponsor-strip","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"0","left":"0","right":"0"},"blockGap":"0"}},"backgroundColor":"white","textColor":"carbon","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull apex-sponsor-strip has-carbon-color has-white-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:0;padding-bottom:0;padding-left:0">
<!-- wp:paragraph {"align":"center","className":"is-style-apex-eyebrow","textColor":"carbon"} -->
<p class="has-text-align-center is-style-apex-eyebrow has-carbon-color has-text-color">Powered by our partners</p>
<!-- /wp:paragraph -->

<!-- wp:apex/sponsors {"align":"full"} /-->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"apex-footer__main apex-texture-grid","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|80"}}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull apex-footer__main apex-texture-grid has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--80)">
<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"}}}} -->
<div class="wp-block-columns alignwide">
<!-- wp:column {"width":"38%","style":{"spacing":{"blockGap":"var:preset|spacing|30"}}} -->
<div class="wp-block-column" style="flex-basis:38%">
<!-- wp:site-logo {"width":220,"shouldSyncIcon":false,"className":"is-footer-logo"} /-->

<!-- wp:paragraph {"textColor":"smoke"} -->
<p class="has-smoke-color has-text-color">Drift, grip and full-throttle competition across Idaho. Drivers of every skill level, spectators welcome, and a paddock that sticks around after the track goes cold.</p>
<!-- /wp:paragraph -->

<?php echo apex_idaho_social_links_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- block markup built from escaped URLs. ?>
</div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column">
<!-- wp:heading {"fontSize":"small"} -->
<h2 class="wp-block-heading has-small-font-size">Explore</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"apex-footer__links"} -->
<ul class="wp-block-list apex-footer__links"><!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/events/' ); ?>">Events</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/media/' ); ?>">Media</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/shop/' ); ?>">Shop</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/about/' ); ?>">About</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column">
<!-- wp:heading {"fontSize":"small"} -->
<h2 class="wp-block-heading has-small-font-size">Drivers</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"apex-footer__links"} -->
<ul class="wp-block-list apex-footer__links"><!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/tech-rules/' ); ?>">Tech &amp; Rules</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/kbd-comp/' ); ?>">KBD Standings</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/product/media-package/' ); ?>">Media Package</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->

<!-- wp:column {"style":{"spacing":{"blockGap":"var:preset|spacing|20"}}} -->
<div class="wp-block-column">
<!-- wp:heading {"fontSize":"small"} -->
<h2 class="wp-block-heading has-small-font-size">Get Involved</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"apex-footer__links"} -->
<ul class="wp-block-list apex-footer__links"><!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/sponsors/' ); ?>">Sponsors</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $apex_link( '/media/media-application/' ); ?>">Media Application</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"apex-footer__bottom","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull apex-footer__bottom has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30)">
<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group alignwide">
<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"apex-idaho/site","args":{"key":"copyright"}}}},"textColor":"smoke","fontSize":"small"} -->
<p class="has-smoke-color has-text-color has-small-font-size">© APEX Idaho. All rights reserved.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"textColor":"smoke","fontSize":"small"} -->
<p class="has-smoke-color has-text-color has-small-font-size">Built in Idaho. Fueled by tire smoke.</p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:separator {"align":"full","className":"is-style-apex-checker"} -->
<hr class="wp-block-separator alignfull has-alpha-channel-opacity is-style-apex-checker"/>
<!-- /wp:separator -->

</div>
<!-- /wp:group -->
