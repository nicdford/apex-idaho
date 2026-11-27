<?php
/**
 * apex/partner-photo
 *
 * @var array $attributes Block attributes.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

$apex_slug     = isset( $attributes['slug'] ) ? sanitize_title( $attributes['slug'] ) : '';
$apex_initials = isset( $attributes['initials'] ) ? $attributes['initials'] : '';
$apex_alt      = isset( $attributes['alt'] ) ? $attributes['alt'] : '';
$apex_photo    = apex_idaho_partner_photo( $apex_slug );

if ( ! $apex_initials && $apex_slug ) {
	$apex_initials = strtoupper(
		implode(
			'',
			array_map(
				static function ( $part ) {
					return substr( $part, 0, 1 );
				},
				array_slice( explode( '-', $apex_slug ), 0, 2 )
			)
		)
	);
}

$apex_wrapper = get_block_wrapper_attributes( array( 'class' => 'apex-partner-photo' . ( $apex_photo ? '' : ' is-placeholder' ) ) );
?>
<figure <?php echo $apex_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php if ( $apex_photo ) : ?>
		<img src="<?php echo esc_url( $apex_photo['url'] ); ?>" alt="<?php echo esc_attr( $apex_alt ); ?>" loading="lazy" decoding="async" style="object-position:<?php echo esc_attr( $apex_photo['focal'] ); ?>">
	<?php else : ?>
		<span class="apex-partner-photo__initials" aria-hidden="true"><?php echo esc_html( $apex_initials ); ?></span>
	<?php endif; ?>
</figure>
