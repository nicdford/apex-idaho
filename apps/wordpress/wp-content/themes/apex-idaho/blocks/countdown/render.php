<?php
/**
 * apex/countdown
 *
 * Server-renders the current values so there is no "--" flash and the block
 * still reads correctly without JavaScript; view.js then ticks it live.
 * Renders nothing for visitors when there is no future target.
 *
 * @var array $attributes Block attributes.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

$apex_source = isset( $attributes['source'] ) && 'date' === $attributes['source'] ? 'date' : 'next-event';
$apex_label  = isset( $attributes['label'] ) ? $attributes['label'] : '';
$apex_title  = isset( $attributes['title'] ) ? $attributes['title'] : '';
$apex_url    = isset( $attributes['url'] ) ? $attributes['url'] : '';
$apex_meta   = '';
$apex_target = 0;

if ( 'next-event' === $apex_source ) {
	foreach ( apex_idaho_upcoming_events( 3 ) as $apex_event ) {
		$apex_ts = apex_idaho_js_timestamp( $apex_event['start'] );
		// The list view includes events already under way; count down to the
		// first one that has not started.
		if ( $apex_ts > time() * 1000 ) {
			$apex_target = $apex_ts;
			$apex_title  = $apex_title ? $apex_title : $apex_event['title'];
			$apex_url    = $apex_url ? $apex_url : $apex_event['url'];
			$apex_meta   = implode( ' · ', array_filter( array( apex_idaho_short_date_range( $apex_event['start'], $apex_event['end'] ), $apex_event['venue'] ) ) );
			break;
		}
	}
} elseif ( ! empty( $attributes['date'] ) ) {
	$apex_target = apex_idaho_js_timestamp( str_replace( 'T', ' ', $attributes['date'] ) );
}

if ( $apex_target <= time() * 1000 ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'apex-countdown is-empty' ) ) . '><p>' . esc_html__( 'Countdown hidden: no upcoming event on the calendar (or the date is in the past).', 'apex-idaho' ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

$apex_left  = (int) floor( ( $apex_target - time() * 1000 ) / 1000 );
$apex_units = array(
	'd' => array( (int) floor( $apex_left / 86400 ), __( 'Days', 'apex-idaho' ) ),
	'h' => array( (int) floor( $apex_left / 3600 ) % 24, __( 'Hrs', 'apex-idaho' ) ),
	'm' => array( (int) floor( $apex_left / 60 ) % 60, __( 'Min', 'apex-idaho' ) ),
	's' => array( $apex_left % 60, __( 'Sec', 'apex-idaho' ) ),
);

$apex_wrapper = get_block_wrapper_attributes(
	array(
		'class'                => 'apex-countdown',
		'data-apex-countdown'  => (string) $apex_target,
		'data-apex-done-label' => __( 'Green flag — on track now', 'apex-idaho' ),
	)
);
?>
<div <?php echo $apex_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $apex_label || $apex_title ) : ?>
		<p class="apex-countdown__label">
			<?php if ( $apex_label ) : ?>
				<span class="apex-countdown__kicker"><?php echo esc_html( $apex_label ); ?></span>
			<?php endif; ?>
			<?php if ( $apex_title && $apex_url ) : ?>
				<a class="apex-countdown__title" href="<?php echo esc_url( $apex_url ); ?>"><?php echo esc_html( $apex_title ); ?></a>
			<?php elseif ( $apex_title ) : ?>
				<span class="apex-countdown__title"><?php echo esc_html( $apex_title ); ?></span>
			<?php endif; ?>
		</p>
	<?php endif; ?>
	<div class="apex-countdown__clock" role="timer">
		<?php foreach ( $apex_units as $apex_key => $apex_unit ) : ?>
			<span class="apex-countdown__unit">
				<b data-unit="<?php echo esc_attr( $apex_key ); ?>"><?php echo esc_html( 'd' === $apex_key ? (string) $apex_unit[0] : str_pad( (string) $apex_unit[0], 2, '0', STR_PAD_LEFT ) ); ?></b>
				<small><?php echo esc_html( $apex_unit[1] ); ?></small>
			</span>
		<?php endforeach; ?>
	</div>
	<?php if ( $apex_meta ) : ?>
		<p class="apex-countdown__meta"><?php echo esc_html( $apex_meta ); ?></p>
	<?php endif; ?>
</div>
