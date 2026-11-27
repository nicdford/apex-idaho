<?php
/**
 * Title: Media partners grid
 * Slug: apex-idaho/media-partners
 * Categories: apex-sections
 * Keywords: media, photographers, partners, team, cards
 * Viewport Width: 1440
 * Description: Cards for each official media partner. Photos come from the Media Partner posts (featured image + focal point); names, outlets and links are editable text.
 *
 * @package apex-idaho
 */

/*
 * Partner details as on the old Media template. `page` = has a Media Partner
 * post with its own page; `links` = [label, url] (url '' renders plain text).
 */
$apex_partners = array(
	'Official'     => array(
		array( 'danielle-choutdara', 'DC', 'Photography', 'Danielle Choutdara', 'DC Photography', true, array( array( '@dc_photography444', 'https://www.instagram.com/dc_photography444' ), array( 'daniellelea444@gmail.com', 'mailto:daniellelea444@gmail.com' ) ) ),
		array( 'lyndon-hunter', 'LH', 'Photography', 'Lyndon Hunter', 'LyndonNotFound', true, array( array( '@lyndonnotfound', 'https://www.instagram.com/lyndonnotfound' ), array( 'lyndonhunter404@gmail.com', 'mailto:lyndonhunter404@gmail.com' ), array( 'lyndonnotfound.squarespace.com', 'https://lyndonnotfound.squarespace.com' ) ) ),
		array( 'mathew-perez', 'MP', 'Videography', 'Mathew Perez', 'Lucid Videography', true, array( array( '@lucid_videography', 'https://www.instagram.com/lucid_videography' ), array( 'perezhouse.mlp@gmail.com', 'mailto:perezhouse.mlp@gmail.com' ), array( 'YouTube channel', '' ) ) ),
		array( 'orion-martin', 'OM', 'Photography', 'Orion Martin', 'Instant Crush', true, array( array( '@instantcrush.photo', 'https://www.instagram.com/instantcrush.photo' ), array( 'orionmartinmarketing@gmail.com', 'mailto:orionmartinmarketing@gmail.com' ), array( 'instantcrushphoto.darkroom.com', 'https://instantcrushphoto.darkroom.com' ) ) ),
		array( 'jordan-van-diest', 'JV', 'Photography & Media', 'Jordan Van Diest', 'Platinum Media', true, array( array( '@platinummediallc', 'https://www.instagram.com/platinummediallc' ), array( 'platinummedia208@gmail.com', 'mailto:platinummedia208@gmail.com' ), array( 'linktr.ee/platinummediallc', 'https://linktr.ee/platinummediallc' ) ) ),
	),
	'New for 2026' => array(
		array( 'caleb-anderson', 'CA', 'Drone', 'Caleb Anderson', 'Caleb Aerials', false, array( array( '@caleb.aerials', 'https://www.instagram.com/caleb.aerials' ), array( 'caleb.aerials@gmail.com', 'mailto:caleb.aerials@gmail.com' ), array( 'linktr.ee/caleb.aerials', 'https://linktr.ee/caleb.aerials' ) ) ),
		array( 'john-dow-ii', 'JD', 'Photography', 'John Dow II', 'F83 Photos', false, array( array( '@f83photos', 'https://www.instagram.com/f83photos' ), array( 'f83photos@icloud.com', 'mailto:f83photos@icloud.com' ), array( 'f83photos.com', 'https://f83photos.com' ) ) ),
		array( 'john-kisiel', 'JK', 'Photography', 'John Kisiel', 'John Kisiel Photography', false, array( array( '@john.kisiel', 'https://www.instagram.com/john.kisiel' ), array( 'johnraymondkisiel@gmail.com', 'mailto:johnraymondkisiel@gmail.com' ), array( 'johnkisielphotography.pixieset.com', 'https://johnkisielphotography.pixieset.com' ) ) ),
	),
);

$apex_first_group = true;
?>
<!-- wp:group {"tagName":"section","anchor":"partners","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"backgroundColor":"carbon","layout":{"type":"constrained"}} -->
<section id="partners" class="wp-block-group alignfull has-carbon-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)">
<?php foreach ( $apex_partners as $apex_group_label => $apex_group ) : ?>
<?php if ( $apex_first_group ) : ?>
<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow"><?php echo esc_html( $apex_group_label ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Media <strong>partners</strong></h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->
<?php else : ?>
<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow"><?php echo esc_html( $apex_group_label ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:separator {"className":"is-style-apex-chevrons","style":{"layout":{"selfStretch":"fill"}}} -->
<hr class="wp-block-separator has-alpha-channel-opacity is-style-apex-chevrons"/>
<!-- /wp:separator --></div>
<!-- /wp:group -->
<?php endif; ?>

<!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"grid","minimumColumnWidth":"18rem"}} -->
<div class="wp-block-group alignwide">
<?php
foreach ( $apex_group as $apex_p ) :
	list( $apex_slug, $apex_initials, $apex_specialty, $apex_name, $apex_biz, $apex_has_page, $apex_links ) = $apex_p;
	?>
<!-- wp:group {"className":"apex-partner apex-card is-style-section-panel","style":{"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"},"blockGap":"0"}},"layout":{"type":"default"}} -->
<div class="wp-block-group apex-partner apex-card is-style-section-panel" style="padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0"><!-- wp:apex/partner-photo {"slug":"<?php echo esc_attr( $apex_slug ); ?>","initials":"<?php echo esc_attr( $apex_initials ); ?>","alt":"<?php echo esc_attr( $apex_name ); ?>"} /-->

<!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|30","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|10"}},"layout":{"type":"default"}} -->
<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:paragraph {"className":"is-style-apex-eyebrow","fontSize":"x-small"} -->
<p class="is-style-apex-eyebrow has-x-small-font-size"><?php echo esc_html( $apex_specialty ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3,"fontSize":"xx-large"} -->
<h3 class="wp-block-heading has-xx-large-font-size"><?php if ( $apex_has_page ) : ?><a href="<?php echo esc_url( home_url( '/media-partner/' . $apex_slug . '/' ) ); ?>"><?php echo esc_html( $apex_name ); ?></a><?php else : ?><?php echo esc_html( $apex_name ); ?><?php endif; ?></h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"textColor":"smoke"} -->
<p class="has-smoke-color has-text-color"><?php echo esc_html( $apex_biz ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list {"className":"apex-partner__links"} -->
<ul class="wp-block-list apex-partner__links"><?php foreach ( $apex_links as $apex_link ) : ?><!-- wp:list-item -->
<li><?php if ( $apex_link[1] ) : ?><a href="<?php echo esc_url( $apex_link[1] ); ?>"><?php echo esc_html( $apex_link[0] ); ?></a><?php else : ?><?php echo esc_html( $apex_link[0] ); ?><?php endif; ?></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<?php endforeach; ?>
</div>
<!-- /wp:group -->
	<?php
	$apex_first_group = false;
endforeach;
?>
</section>
<!-- /wp:group -->
