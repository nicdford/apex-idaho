<?php
/**
 * apex/cart-link
 *
 * @var array $attributes Block attributes.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_cart_url' ) ) {
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'apex-cart-link is-empty' ) ) . '>' . esc_html__( 'Cart (shows when WooCommerce is active)', 'apex-idaho' ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return;
}

$apex_label = isset( $attributes['label'] ) && '' !== $attributes['label'] ? $attributes['label'] : __( 'Cart', 'apex-idaho' );
$apex_count = ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;

$apex_wrapper = get_block_wrapper_attributes( array( 'class' => 'apex-cart-link' ) );
?>
<div <?php echo $apex_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="apex-cart-link__a">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="square" aria-hidden="true" focusable="false"><path d="M3 4h3l2.4 11.2a1 1 0 0 0 1 .8h8.9a1 1 0 0 0 1-.8L21 8H7"/><circle cx="10" cy="20" r="1.4"/><circle cx="18" cy="20" r="1.4"/></svg>
		<span class="apex-cart-link__label"><?php echo esc_html( $apex_label ); ?></span>
		<span class="apex-cart-link__count<?php echo $apex_count ? '' : ' is-zero'; ?>">
			<span class="screen-reader-text"><?php esc_html_e( 'Items in cart:', 'apex-idaho' ); ?></span>
			<?php echo (int) $apex_count; ?>
		</span>
	</a>
</div>
