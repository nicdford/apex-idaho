<?php
/**
 * apex/upcoming-events
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner content (unused).
 * @var WP_Block $block      Block instance.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

$apex_layout = isset( $attributes['layout'] ) && in_array( $attributes['layout'], array( 'cards', 'list', 'ticker' ), true ) ? $attributes['layout'] : 'cards';
$apex_count  = isset( $attributes['count'] ) ? (int) $attributes['count'] : 3;
$apex_cta    = isset( $attributes['ctaLabel'] ) && '' !== $attributes['ctaLabel'] ? $attributes['ctaLabel'] : __( 'Details & Tickets', 'apex-idaho' );
$apex_empty  = isset( $attributes['emptyMessage'] ) ? $attributes['emptyMessage'] : '';
$apex_events = apex_idaho_upcoming_events( $apex_count, isset( $attributes['category'] ) ? $attributes['category'] : '' );

$apex_wrapper = get_block_wrapper_attributes(
	array( 'class' => 'apex-events apex-events--' . $apex_layout . ( $apex_events ? '' : ' is-empty' ) )
);

/*
 * Ticker: inline items for the header strip. Each is a link to the event.
 */
if ( 'ticker' === $apex_layout ) {
	echo '<div ' . $apex_wrapper . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	if ( ! $apex_events ) {
		if ( $apex_empty ) {
			echo '<span class="apex-ticker-item">' . esc_html( $apex_empty ) . '</span>';
		}
	}
	foreach ( $apex_events as $apex_i => $apex_event ) {
		$apex_where = $apex_event['venue'] ? $apex_event['venue'] : $apex_event['city'];
		printf(
			'<span class="apex-ticker-item"><a href="%1$s"><strong>%2$s</strong> %3$s<span class="apex-ticker-item__meta">%4$s%5$s</span></a></span>',
			esc_url( $apex_event['url'] ),
			0 === $apex_i ? esc_html__( 'Next up', 'apex-idaho' ) : esc_html( apex_idaho_short_date_range( $apex_event['start'], $apex_event['end'] ) ),
			esc_html( $apex_event['title'] ),
			0 === $apex_i ? esc_html( apex_idaho_short_date_range( $apex_event['start'], $apex_event['end'] ) ) : '',
			$apex_where ? ' · ' . esc_html( $apex_where ) : ''
		);
	}
	echo '</div>';
	return;
}

if ( ! $apex_events ) {
	printf(
		'<div %1$s><div class="apex-events__empty"><p class="apex-events__empty-flag">%2$s</p><p class="apex-events__empty-text">%3$s</p></div></div>',
		$apex_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'Pit lane closed', 'apex-idaho' ),
		esc_html( $apex_empty )
	);
	return;
}

/**
 * Date badge parts: day ("13" or "26–27") and month ("Jun" or "Jun–Jul").
 */
$apex_badge = static function ( $start, $end ) {
	$s = $start ? strtotime( $start ) : 0;
	if ( ! $s ) {
		return array( '', '' );
	}
	$e = $end ? strtotime( $end ) : $s;

	if ( gmdate( 'Y-m-d', $s ) === gmdate( 'Y-m-d', $e ) ) {
		return array( date_i18n( 'j', $s ), date_i18n( 'M', $s ) );
	}
	if ( gmdate( 'Y-n', $s ) === gmdate( 'Y-n', $e ) ) {
		return array( date_i18n( 'j', $s ) . '–' . date_i18n( 'j', $e ), date_i18n( 'M', $s ) );
	}
	return array( date_i18n( 'j', $s ), date_i18n( 'M', $s ) . '–' . date_i18n( 'M', $e ) );
};

$apex_show_image = ! isset( $attributes['showImage'] ) || $attributes['showImage'];
?>
<div <?php echo $apex_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'list' === $apex_layout ) : ?>
		<ol class="apex-events__rows" role="list">
			<?php
			foreach ( $apex_events as $apex_i => $apex_event ) :
				$apex_where = implode( ' · ', array_filter( array( $apex_event['venue'], $apex_event['city'] ) ) );
				?>
				<li class="apex-event-row">
					<span class="apex-event-row__pos" aria-hidden="true">P<?php echo (int) ( $apex_i + 1 ); ?></span>
					<span class="apex-event-row__date"><?php echo esc_html( apex_idaho_short_date_range( $apex_event['start'], $apex_event['end'] ) ); ?></span>
					<span class="apex-event-row__main">
						<a class="apex-event-row__title" href="<?php echo esc_url( $apex_event['url'] ); ?>"><?php echo esc_html( $apex_event['title'] ); ?></a>
						<?php if ( $apex_where ) : ?>
							<span class="apex-event-row__venue"><?php echo esc_html( $apex_where ); ?></span>
						<?php endif; ?>
					</span>
					<?php if ( $apex_event['cost'] ) : ?>
						<span class="apex-event-row__cost"><?php echo esc_html( $apex_event['cost'] ); ?></span>
					<?php endif; ?>
					<span class="apex-event-row__go" aria-hidden="true">&rarr;</span>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php else : ?>
		<ol class="apex-events__cards" role="list">
			<?php
			foreach ( $apex_events as $apex_i => $apex_event ) :
				list( $apex_day, $apex_month ) = $apex_badge( $apex_event['start'], $apex_event['end'] );
				$apex_time                     = $apex_event['start'] ? date_i18n( 'D, M j · g:i A', strtotime( $apex_event['start'] ) ) : '';
				$apex_where                    = implode( ' · ', array_filter( array( $apex_event['venue'], $apex_event['city'] ) ) );
				?>
				<li class="apex-event-card<?php echo $apex_show_image ? '' : ' has-no-media'; ?>">
					<?php if ( $apex_show_image ) : ?>
						<div class="apex-event-card__media" aria-hidden="true">
							<?php // Events without a featured image use the theme's drift photo, as the single event page does. ?>
							<img src="<?php echo esc_url( $apex_event['image'] ? $apex_event['image'] : get_theme_file_uri( 'assets/images/hero-drift.jpg' ) ); ?>" alt="" loading="lazy" decoding="async">
							<span class="apex-event-card__num"><?php echo esc_html( str_pad( (string) ( $apex_i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						</div>
					<?php endif; ?>
					<div class="apex-event-card__body">
						<p class="apex-event-card__date">
							<span class="apex-event-card__day"><?php echo esc_html( $apex_day ); ?></span>
							<span class="apex-event-card__month"><?php echo esc_html( $apex_month ); ?></span>
						</p>
						<h3 class="apex-event-card__title">
							<a href="<?php echo esc_url( $apex_event['url'] ); ?>"><?php echo esc_html( $apex_event['title'] ); ?></a>
						</h3>
						<?php if ( $apex_where ) : ?>
							<p class="apex-event-card__where"><?php echo esc_html( $apex_where ); ?></p>
						<?php endif; ?>
						<?php if ( $apex_time ) : ?>
							<p class="apex-event-card__when"><?php echo esc_html( $apex_time ); ?></p>
						<?php endif; ?>
						<p class="apex-event-card__foot">
							<?php if ( $apex_event['cost'] ) : ?>
								<span class="apex-event-card__cost"><?php echo esc_html( $apex_event['cost'] ); ?></span>
							<?php endif; ?>
							<span class="apex-event-card__cta" aria-hidden="true"><?php echo esc_html( $apex_cta ); ?> <span class="apex-arrow">&rarr;</span></span>
						</p>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
</div>
