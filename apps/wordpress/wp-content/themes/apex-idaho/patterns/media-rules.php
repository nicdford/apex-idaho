<?php
/**
 * Title: Media rules (two lists)
 * Slug: apex-idaho/media-rules
 * Categories: apex-sections
 * Keywords: rules, requirements, policy, media, lists, two columns
 * Viewport Width: 1440
 * Description: Light "paper" section with two titled rule lists side by side and a footnote.
 *
 * @package apex-idaho
 */

$apex_rules = array(
	'Application requirements' => array(
		'You must submit an application, and be accepted to be on track at any APEX Idaho event. Having a vest and a camera will not get you in for free, or allow you on track.',
		'You must own or work for a professional media business, publication, or have a website that displays your work.',
		'You must provide examples of your work, and be ready to provide references if asked.',
		'You cannot shoot on your phone as your primary equipment, and only have an Instagram profile.',
		'Approved media members that provided media to APEX Idaho for promotional material in previous years, will receive preference in approval.',
		'Approval preference will go to those who can commit to attending most, if not all events.',
		'If you are approved, you will receive an ID card that is good for the 2025 season. You will also receive a pink Hi-Vis vest to wear at all APEX Idaho events.',
	),
	'Approved media must'      => array(
		'Be interviewed at first track visit with an APEX Idaho official to verify credentials.',
		'Have your pink Hi-Vis vest on, and only enter and exit the track when it is appropriate and safe to do so.',
		'Maintain a level of professionalism, and respect of others and the track.',
		'Attend ALL media meetings ON TIME. NO EXCEPTIONS. If you cannot make it to the meetings, you will not be approved for that event.',
		"Upload media to APEX Idaho for promotional use via Google Drive within one week of the event's end. Uploads must be at minimum an entry fee's worth.",
		'If posting your work online, tag @apex_idaho on Instagram and APEX Idaho on Facebook.',
		'Provide links to your work on the Facebook APEX Idaho group or event page.',
		'Replacement cost for lost ID card or Hi-Vis vest is $40 each.',
	),
);
?>
<!-- wp:group {"tagName":"section","anchor":"become","align":"full","className":"is-style-section-paper","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|50"}},"layout":{"type":"constrained"}} -->
<section id="become" class="wp-block-group alignfull is-style-section-paper" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:group {"align":"wide","style":{"spacing":{"blockGap":"var:preset|spacing|20"}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Join the team</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Become a media partner</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"fontSize":"large"} -->
<p class="has-large-font-size">Media rules &amp; requirements</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide">
<?php foreach ( $apex_rules as $apex_title => $apex_items ) : ?>
<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"},"border":{"top":{"color":"var:preset|color|apex-pink","width":"5px","style":"solid"}}},"backgroundColor":"white"} -->
<div class="wp-block-column has-white-background-color has-background" style="border-top-color:var(--wp--preset--color--apex-pink);border-top-style:solid;border-top-width:5px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)"><!-- wp:heading {"level":3,"fontSize":"x-large"} -->
<h3 class="wp-block-heading has-x-large-font-size"><?php echo esc_html( $apex_title ); ?></h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-apex-diamonds"} -->
<ul class="wp-block-list is-style-apex-diamonds"><?php foreach ( $apex_items as $apex_item ) : ?><!-- wp:list-item -->
<li><?php echo esc_html( $apex_item ); ?></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list --></div>
<!-- /wp:column -->
<?php endforeach; ?>
</div>
<!-- /wp:columns -->

<!-- wp:paragraph {"align":"center","style":{"typography":{"fontStyle":"italic"}},"fontSize":"small"} -->
<p class="has-text-align-center has-small-font-size" style="font-style:italic">APEX Idaho reserves the right to revoke any media privileges at any time, for any reason.</p>
<!-- /wp:paragraph --></section>
<!-- /wp:group -->
