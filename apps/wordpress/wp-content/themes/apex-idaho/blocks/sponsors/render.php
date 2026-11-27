<?php
/**
 * apex/sponsors
 *
 * Logos sit on a white board. That is deliberate: every sponsor's "convert to
 * black" / "invert" setting was tuned against white, so the board keeps those
 * logos legible on an otherwise dark site.
 *
 * @var array $attributes Block attributes.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

$apex_layout   = isset( $attributes['layout'] ) && 'grid' === $attributes['layout'] ? 'grid' : 'marquee';
$apex_names    = ! empty( $attributes['showNames'] );
$apex_sponsors = apex_idaho_get_sponsors();

if ( ! $apex_sponsors ) {
	// Say why it is empty in the editor; render nothing for visitors.
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'apex-sponsors is-empty' ) ) . '><p>' . esc_html__( 'No sponsors with a logo yet. Add them under Sponsors in the admin menu.', 'apex-idaho' ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

/**
 * One logo. $decorative marks the second, duplicated pass of the marquee.
 */
$apex_item = static function ( $sponsor, $decorative, $show_name ) {
	$style = $sponsor['filter'] ? ' style="--apex-logo-filter:' . esc_attr( $sponsor['filter'] ) . '"' : '';
	$class = 'apex-sponsor' . ( $sponsor['invert'] ? ' is-inverted' : '' ) . ( $sponsor['filter'] ? ' has-filter' : '' );
	$img   = sprintf(
		'<img src="%1$s" alt="%2$s" loading="lazy" decoding="async"%3$s>',
		esc_url( $sponsor['logo'] ),
		// Empty alt on the duplicate marquee pass, and when the name is printed
		// beside the logo (otherwise screen readers hear it twice).
		( $decorative || $show_name ) ? '' : esc_attr( $sponsor['title'] ),
		$style
	);
	$name  = $show_name ? '<span class="apex-sponsor__name">' . esc_html( $sponsor['title'] ) . '</span>' : '';

	if ( $sponsor['url'] ) {
		$inner = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener"%2$s>%3$s%4$s</a>',
			esc_url( $sponsor['url'] ),
			$decorative ? ' tabindex="-1"' : '',
			$img,
			$name
		);
	} else {
		$inner = '<span class="apex-sponsor__inner">' . $img . $name . '</span>';
	}

	return '<li class="' . esc_attr( $class ) . '"' . ( $decorative ? ' aria-hidden="true"' : '' ) . '>' . $inner . '</li>';
};

$apex_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'apex-sponsors apex-sponsors--' . $apex_layout,
		'style' => 'marquee' === $apex_layout ? '--apex-marquee-duration:' . max( 24, count( $apex_sponsors ) * 4 ) . 's' : '',
	)
);
?>
<div <?php echo $apex_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( 'grid' === $apex_layout ) : ?>
		<ul class="apex-sponsors__grid" role="list">
			<?php
			foreach ( $apex_sponsors as $apex_sponsor ) {
				echo $apex_item( $apex_sponsor, false, $apex_names ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</ul>
	<?php else : ?>
		<div class="apex-sponsors__viewport">
			<ul class="apex-sponsors__track" role="list" aria-label="<?php esc_attr_e( 'Sponsors', 'apex-idaho' ); ?>">
				<?php
				// Two passes for a seamless loop; the second is hidden from AT and the tab order.
				foreach ( array( false, true ) as $apex_decorative ) {
					foreach ( $apex_sponsors as $apex_sponsor ) {
						echo $apex_item( $apex_sponsor, $apex_decorative, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
				?>
			</ul>
		</div>
	<?php endif; ?>
</div>
