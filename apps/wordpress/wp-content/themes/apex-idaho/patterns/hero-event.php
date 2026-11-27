<?php
/**
 * Title: Hero: Event landing (split)
 * Slug: apex-idaho/hero-event
 * Categories: apex-hero, apex-events
 * Keywords: hero, event, festival, landing, split
 * Viewport Width: 1440
 * Description: Event name or logo, dates and location, intro and ticket buttons beside an angled photo.
 *
 * @package apex-idaho
 */

$apex_photo = esc_url( get_theme_file_uri( 'assets/images/hero-drift.jpg' ) );

// The Festival of Speed lockup the old template used, when it exists here.
$apex_uploads = wp_get_upload_dir();
$apex_logo    = '';
if ( ! empty( $apex_uploads['basedir'] ) && file_exists( $apex_uploads['basedir'] . '/2024/02/MVFOS-Stacked.png' ) ) {
	$apex_logo = esc_url( $apex_uploads['baseurl'] . '/2024/02/MVFOS-Stacked.png' );
}
?>
<!-- wp:group {"tagName":"section","align":"full","className":"apex-texture-night apex-cut-bottom apex-into-asphalt","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"calc(var(--wp--preset--spacing--70) + 3rem)"}}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull apex-texture-night apex-cut-bottom apex-into-asphalt has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:calc(var(--wp--preset--spacing--70) + 3rem)"><!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|60"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"52%","style":{"spacing":{"blockGap":"var:preset|spacing|40"}}} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">APEX Idaho presents</p>
<!-- /wp:paragraph -->

<?php if ( $apex_logo ) : ?>
<!-- wp:image {"width":"400px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="<?php echo $apex_logo; ?>" alt="Magic Valley Festival of Speed" style="width:400px"/></figure>
<!-- /wp:image -->
<?php else : ?>
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Magic Valley<br><strong>Festival of Speed</strong></h1>
<!-- /wp:heading -->
<?php endif; ?>

<!-- wp:paragraph {"className":"apex-crumb","textColor":"chalk","fontSize":"x-large"} -->
<p class="apex-crumb has-chalk-color has-text-color has-x-large-font-size">May 23–25 ◆ Twin Falls, ID</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"is-style-apex-lede"} -->
<p class="is-style-apex-lede">Three days of drift, grip and full-throttle competition at one of Idaho's premier motorsport events. All skill levels welcome.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-apex-arrow"} -->
<div class="wp-block-button is-style-apex-arrow"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">Buy driver entry</a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/events/' ) ); ?>">Spectator tickets</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","width":"48%"} -->
<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:48%"><!-- wp:image {"aspectRatio":"4/5","scale":"cover","sizeSlug":"large","linkDestination":"none","className":"is-style-apex-angled"} -->
<figure class="wp-block-image size-large is-style-apex-angled"><img src="<?php echo $apex_photo; ?>" alt="Two drift cars in tandem at Magic Valley Speedway" style="aspect-ratio:4/5;object-fit:cover"/></figure>
<!-- /wp:image --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
