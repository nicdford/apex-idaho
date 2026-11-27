<?php
/**
 * Title: Schedule: Day-by-day timing sheets
 * Slug: apex-idaho/schedule-days
 * Categories: apex-events, apex-sections
 * Keywords: schedule, run sheet, timetable, agenda, accordion
 * Viewport Width: 1440
 * Description: One expandable row per day, each holding a timing-sheet table. Edit the tables directly; add a day by duplicating a row.
 *
 * @package apex-idaho
 */

$apex_days = array(
	'Friday · May 23'   => array(
		array( '8:00 AM', 'Gates open / registration' ),
		array( '9:00 AM', 'Tech inspection' ),
		array( '10:00 AM', 'Hot laps — open practice' ),
		array( '12:00 PM', 'Lunch break' ),
		array( '1:00 PM', 'Hot laps — afternoon session' ),
		array( '4:00 PM', 'Track closes' ),
		array( '7:00 PM', 'Driver welcome party' ),
	),
	'Saturday · May 24' => array(
		array( '8:00 AM', 'Gates open' ),
		array( '9:00 AM', 'Hot laps — morning session' ),
		array( '11:00 AM', 'Competition runs begin' ),
		array( '12:30 PM', 'Lunch break' ),
		array( '1:30 PM', 'Competition continues' ),
		array( '3:30 PM', 'Tandem practice' ),
		array( '5:00 PM', 'Media sessions' ),
		array( '7:00 PM', 'Driver party' ),
	),
	'Sunday · May 25'   => array(
		array( '8:00 AM', 'Gates open' ),
		array( '9:00 AM', 'Final competition runs' ),
		array( '11:30 AM', 'Tandem finals' ),
		array( '1:00 PM', 'Lunch break' ),
		array( '2:00 PM', 'Awards ceremony' ),
		array( '3:30 PM', 'Track closes / load out' ),
	),
);
?>
<!-- wp:group {"tagName":"section","align":"full","className":"apex-texture-grid","style":{"spacing":{"padding":{"top":"var:preset|spacing|80","bottom":"var:preset|spacing|80"},"blockGap":"var:preset|spacing|40"}},"backgroundColor":"asphalt","layout":{"type":"constrained","contentSize":"960px"}} -->
<section class="wp-block-group alignfull apex-texture-grid has-asphalt-background-color has-background" style="padding-top:var(--wp--preset--spacing--80);padding-bottom:var(--wp--preset--spacing--80)"><!-- wp:paragraph {"className":"is-style-apex-eyebrow"} -->
<p class="is-style-apex-eyebrow">Run sheet</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Event <strong>schedule</strong></h2>
<!-- /wp:heading -->

<?php
$apex_first = true;
foreach ( $apex_days as $apex_day => $apex_sessions ) :
	?>
<!-- wp:details<?php echo $apex_first ? ' {"showContent":true}' : ''; ?> -->
<details class="wp-block-details"<?php echo $apex_first ? ' open' : ''; ?>><summary><?php echo esc_html( $apex_day ); ?></summary><!-- wp:table {"hasFixedLayout":false,"className":"is-style-apex-timing"} -->
<figure class="wp-block-table is-style-apex-timing"><table><thead><tr><th>Time</th><th>Session</th></tr></thead><tbody><?php foreach ( $apex_sessions as $apex_s ) : ?><tr><td><?php echo esc_html( $apex_s[0] ); ?></td><td><?php echo esc_html( $apex_s[1] ); ?></td></tr><?php endforeach; ?></tbody></table></figure>
<!-- /wp:table --></details>
<!-- /wp:details -->
	<?php
	$apex_first = false;
endforeach;
?>
</section>
<!-- /wp:group -->
