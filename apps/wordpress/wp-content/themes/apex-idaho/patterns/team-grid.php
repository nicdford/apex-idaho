<?php
/**
 * Title: Team grid
 * Slug: apex-idaho/team-grid
 * Categories: apex-sections
 * Keywords: team, crew, staff, people, about, cards
 * Viewport Width: 1440
 * Description: "Meet the team" cards: photo (or initials), role, name and social links. Swap a photo by replacing the image; for someone without one, keep the red initials box.
 *
 * @package apex-idaho
 */

/*
 * The crew as on the old About page. Photo = file in assets/images/team, or ''
 * for the initials placeholder. Links = [label, url].
 */
$apex_team = array(
	array( 'Anthony Barnes', 'Head Beard', 'anthony-barnes.jpg', array( array( 'Facebook', 'https://www.facebook.com/sevensixracing' ), array( 'Instagram', 'https://www.instagram.com/sevensixusa/' ) ) ),
	array( 'Melissa Miles', 'Event Director', 'melissa-miles.jpg', array( array( 'Facebook', 'https://www.facebook.com/melissa.pullin.9/' ) ) ),
	array( 'Danny Wyatt', 'Track Team / Flagger', '', array( array( 'Facebook', 'https://www.facebook.com/daniel.wyatt.9469' ) ) ),
	array( 'Jay Caiola', 'Track Team / Tech', '', array( array( 'Facebook', 'https://www.facebook.com/jay.caiola' ) ) ),
	array( 'Nic Ford', 'Website / Rebar', 'nic-ford.jpg', array( array( 'Facebook', 'https://www.facebook.com/nicdford' ), array( 'Instagram', 'https://www.instagram.com/nicdford' ) ) ),
	array( 'Jordan VanDiest', 'Media / Social Media', 'jordan-vandiest.jpg', array( array( 'Facebook', 'https://www.facebook.com/jordan.vandiest.5' ) ) ),
);

$apex_initials = static function ( $name ) {
	$parts = preg_split( '/\s+/', trim( $name ) );
	return strtoupper( substr( $parts[0], 0, 1 ) . ( count( $parts ) > 1 ? substr( end( $parts ), 0, 1 ) : '' ) );
};
?>
<!-- wp:group {"tagName":"section","anchor":"team","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section id="team" class="wp-block-group alignfull has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">The crew</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Meet the <strong>team</strong></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"grid","minimumColumnWidth":"20rem"}} -->
<div class="wp-block-group alignwide">
<?php
foreach ( $apex_team as $apex_person ) :
	list( $apex_name, $apex_role, $apex_photo, $apex_links ) = $apex_person;
	?>
<!-- wp:group {"className":"apex-partner apex-card is-style-section-panel","style":{"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"},"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group apex-partner apex-card is-style-section-panel" style="padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0">
	<?php if ( $apex_photo ) : ?>
<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"apex-partner-photo"} -->
<figure class="wp-block-image size-full apex-partner-photo"><img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/team/' . $apex_photo ) ); ?>" alt="<?php echo esc_attr( $apex_name ); ?>"/></figure>
<!-- /wp:image -->
	<?php else : ?>
<!-- wp:group {"className":"apex-partner-photo is-placeholder","layout":{"type":"default"}} -->
<div class="wp-block-group apex-partner-photo is-placeholder"><!-- wp:paragraph {"className":"apex-partner-photo__initials"} -->
<p class="apex-partner-photo__initials"><?php echo esc_html( $apex_initials( $apex_name ) ); ?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
	<?php endif; ?>

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"className":"is-style-apex-eyebrow","fontSize":"x-small"} -->
<p class="is-style-apex-eyebrow has-x-small-font-size"><?php echo esc_html( $apex_role ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"xx-large"} -->
<h3 class="wp-block-heading has-xx-large-font-size"><?php echo esc_html( $apex_name ); ?></h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"apex-partner__links"} -->
<ul class="wp-block-list apex-partner__links"><?php foreach ( $apex_links as $apex_link ) : ?><!-- wp:list-item -->
<li><a href="<?php echo esc_url( $apex_link[1] ); ?>"><?php echo esc_html( $apex_link[0] ); ?></a></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group --></section>
<!-- /wp:group -->
