<?php
/**
 * Title: Page: Tech & Rules
 * Slug: apex-idaho/page-tech-rules
 * Categories: apex-pages
 * Keywords: tech, rules, inspection, requirements, conduct, drivers, helmet
 * Block Types: core/post-content
 * Post Types: page, wp_template
 * Viewport Width: 1440
 * Description: Official tech rules (Numbered Rules list) and the driver code of conduct, with a button that prints a clean rules sheet.
 *
 * @package apex-idaho
 */

/*
 * The current rules from the live page (updated March 12, 2026).
 * [ text, sub-items, sub-items numbered? ]. **text** renders bold.
 */
$apex_rules = array(
	array( 'Functional seat belt or harness.', array( 'Non-OEM seats must have a harness.', 'Original seats can use stock seatbelts.' ), false ),
	array( 'Battery strap or tie down.', array(), false ),
	array( 'Functioning tail and brake lights. At least one brake light, one tail light, and one headlight.', array(), false ),
	array( 'All lug nuts are present and secured.', array(), false ),
	array( 'No cracked or broken glass.', array(), false ),
	array( 'All loose items must be removed before getting on track.', array(), false ),
	array(
		'Fire extinguisher: at least 2.5 lbs.',
		array(
			'Mounted within reach of the driver.',
			'Mounted in a way that can be easily removed, quick release system, or equivalent.',
			'Zip tie mounting will not be allowed.',
			'Don’t forget, fire extinguishers are not for your car; they’re for the cars around you. If your car is on fire, GET OUT, don’t try to get your extinguisher.',
		),
		false,
	),
	array( 'No loose body parts or excessively damaged panels.', array(), false ),
	array( 'Must be free of major leaks, with no open reservoirs or containers.', array(), false ),
	array( 'Use of water/water wetter. Coolant is acceptable for stock cars with factory crash bars and stock cooling systems.', array(), false ),
	array( 'Roll bars are required for all convertible vehicles.', array(), false ),
	array( 'Appropriate clothing.', array( 'Long pants.', 'Short sleeve t-shirt (no tank tops).', 'Full-toe coverage shoes.' ), true ),
	array( 'All drivers will have a **full-face** helmet.', array( 'Helmets must be Snell 2020 or newer.', 'DOT, ECE, etc. not allowed.', 'Exclusive to ride-along events: ride-along passengers are allowed to have open face helmets.' ), true ),
	array( 'Functioning throttle return spring for cars with throttle cables.', array(), false ),
	array( 'No Z31’s.', array(), false ),
	array( 'Engine compartment needs to be in an orderly fashion: no fuel lines routing over exhaust manifolds, hoses running to nowhere, etc. Extremely loose items need to be secured. For instance, no wiring should be able to go into a serpentine belt.', array(), false ),
	array( 'All seats in the vehicle that will hold passengers need to have four bolts securing them to the car.', array( 'Recommended seat belt bolts would be at least 8mm or 5/16″ in diameter and Grade 8.' ), false ),
	array( 'Any excessively loud vehicles will get a warning for one event, and must be fixed before the next event.', array( 'Recommend at least one muffler or resonator.' ), true ),
);

$apex_conduct = array(
	'**Drivers must attend morning driver’s meetings.** We will not do personal driver meetings. Please make arrangements with a track official before the day of the event if you absolutely can not make it. All other situations will warrant driving ineligibility, with no refund.',
	'Be alert and pay attention to all flaggers and track officials. If you do not, you will be sent off track. Repeated offenses will result in being ejected from the event.',
	'Keep the pits clean. Use the trash receptacles.',
	'All spills must be cleaned IMMEDIATELY. Water/water wetter is required in the cooling system.',
	'Keep arms inside the vehicle at all times.',
);

$apex_text = static function ( $text ) {
	return preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', esc_html( $text ) );
};

$apex_card_open = '<!-- wp:column {"verticalAlignment":"top","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"},"blockGap":"var:preset|spacing|30"},"border":{"top":{"color":"var:preset|color|red","width":"5px","style":"solid"}}},"backgroundColor":"white"} -->' . "\n"
	. '<div class="wp-block-column is-vertically-aligned-top has-white-background-color has-background" style="border-top-color:var(--wp--preset--color--red);border-top-style:solid;border-top-width:5px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">';

// Rules list as compact block markup (no stray whitespace inside list items).
$apex_rules_list = '<!-- wp:list {"ordered":true,"className":"is-style-apex-rules"} -->' . "\n" . '<ol class="wp-block-list is-style-apex-rules">';
foreach ( $apex_rules as $apex_rule ) {
	list( $apex_rule_text, $apex_sub, $apex_sub_ordered ) = $apex_rule;
	$apex_inner = $apex_text( $apex_rule_text );
	if ( $apex_sub ) {
		$apex_tag    = $apex_sub_ordered ? 'ol' : 'ul';
		$apex_inner .= '<!-- wp:list ' . ( $apex_sub_ordered ? '{"ordered":true} ' : '' ) . '-->' . "\n" . '<' . $apex_tag . ' class="wp-block-list">';
		foreach ( $apex_sub as $apex_item ) {
			$apex_inner .= '<!-- wp:list-item -->' . "\n" . '<li>' . $apex_text( $apex_item ) . '</li>' . "\n" . '<!-- /wp:list-item -->';
		}
		$apex_inner .= '</' . $apex_tag . '>' . "\n" . '<!-- /wp:list -->';
	}
	$apex_rules_list .= '<!-- wp:list-item -->' . "\n" . '<li>' . $apex_inner . '</li>' . "\n" . '<!-- /wp:list-item -->';
}
$apex_rules_list .= '</ol>' . "\n" . '<!-- /wp:list -->';

echo apex_idaho_page_band_markup( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'Official · Updated March 12, 2026',
	'Tech &amp; <strong>rules</strong>',
	'Tech rules and car requirements for every APEX Idaho event, and the code of conduct every driver follows.',
	array( array( 'Print the rules sheet', '#print', 'outline' ) ),
	'paper'
);
?>

<!-- wp:group {"tagName":"section","align":"full","className":"is-style-section-paper","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"}}},"layout":{"type":"constrained"}} -->
<section class="wp-block-group alignfull is-style-section-paper" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:columns {"verticalAlignment":"top","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|40","left":"var:preset|spacing|40"}}}} -->
<div class="wp-block-columns alignwide are-vertically-aligned-top">
<?php echo $apex_card_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">All event tech rules and car requirements</h2>
<!-- /wp:heading -->

<?php echo $apex_rules_list; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped text above. ?></div>
<!-- /wp:column -->

<?php echo $apex_card_open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><!-- wp:heading {"fontSize":"x-large"} -->
<h2 class="wp-block-heading has-x-large-font-size">Driver code of conduct</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"is-style-apex-diamonds"} -->
<ul class="wp-block-list is-style-apex-diamonds"><?php foreach ( $apex_conduct as $apex_item ) : ?><!-- wp:list-item -->
<li><?php echo $apex_text( $apex_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></li>
<!-- /wp:list-item --><?php endforeach; ?></ul>
<!-- /wp:list -->

<?php echo apex_idaho_buttons_markup( array( array( 'Find an event', home_url( '/events/' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></section>
<!-- /wp:group -->
