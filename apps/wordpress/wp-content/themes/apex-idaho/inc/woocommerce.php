<?php
/**
 * WooCommerce behaviour carried over from the child theme.
 *
 * The two coupon gates that were commented out in bb-theme-child are not
 * ported: they were unhooked, and the gate that runs in production lives in
 * plugins/special-coupon-checkout-gate.php.
 *
 * @package apex-idaho
 */

defined( 'ABSPATH' ) || exit;

/**
 * Auto-complete every order on the thank-you page.
 */
add_action( 'woocommerce_thankyou', 'custom_woocommerce_auto_complete_order' );
if ( ! function_exists( 'custom_woocommerce_auto_complete_order' ) ) {
	function custom_woocommerce_auto_complete_order( $order_id ) {
		if ( ! $order_id ) {
			return;
		}

		$order = wc_get_order( $order_id );
		if ( $order ) {
			$order->update_status( 'completed' );
		}
	}
}

if ( ! function_exists( 'get_order_by_id' ) ) {
	function get_order_by_id( $order_id ) {
		if ( class_exists( 'WooCommerce' ) ) {
			return wc_get_order( $order_id );
		}
	}
}

if ( ! function_exists( 'order_has_coupon' ) ) {
	function order_has_coupon( $coupon, $order ) {
		return in_array( $coupon, $order->get_coupon_codes(), true );
	}
}

/* ============================================================
 * Media Package — require car photo upload on add-to-cart
 * ============================================================ */

if ( ! defined( 'APEX_MEDIA_PACKAGE_PRODUCT_ID' ) ) {
	define( 'APEX_MEDIA_PACKAGE_PRODUCT_ID', 2825 );
}

function apex_is_media_package_product( $product_id ) {
	return intval( $product_id ) === (int) APEX_MEDIA_PACKAGE_PRODUCT_ID;
}

// The single-product form needs multipart encoding for the file input.
add_action( 'woocommerce_before_single_product', 'apex_idaho_media_package_multipart' );
function apex_idaho_media_package_multipart() {
	global $product;
	if ( ! $product || ! apex_is_media_package_product( $product->get_id() ) ) {
		return;
	}
	add_action(
		'woocommerce_before_add_to_cart_form',
		function () {
			echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form.cart");if(f){f.setAttribute("enctype","multipart/form-data");}});</script>';
		}
	);
}

// Upload + description fields on the Media Package product page.
add_action( 'woocommerce_before_add_to_cart_button', 'apex_idaho_media_package_fields' );
function apex_idaho_media_package_fields() {
	global $product;
	if ( ! $product || ! apex_is_media_package_product( $product->get_id() ) ) {
		return;
	}
	?>
	<div class="apex-car-photo-field">
		<p class="apex-car-photo-field__title">
			Help our team spot your car on track <span class="apex-car-photo-field__req" aria-hidden="true">*</span>
		</p>
		<p class="apex-car-photo-field__hint">
			Upload a photo <strong>or</strong> describe your car below — at least one is required.
		</p>

		<label for="apex_car_photo">Photo of your car</label>
		<p class="apex-car-photo-field__hint">JPG, PNG, HEIC, or WEBP. Max 10 MB.</p>
		<input type="file" id="apex_car_photo" name="apex_car_photo"
			accept="image/jpeg,image/png,image/webp,image/heic,image/heif">

		<label for="apex_car_description">Or describe your car</label>
		<p class="apex-car-photo-field__hint">e.g. "Red 2018 Mustang GT, black wheels, #42 on driver door"</p>
		<textarea id="apex_car_description" name="apex_car_description" rows="3" maxlength="500"></textarea>
	</div>
	<?php
}

// Validate: require photo OR description.
add_filter( 'woocommerce_add_to_cart_validation', 'apex_idaho_media_package_validate', 10, 3 );
function apex_idaho_media_package_validate( $passed, $product_id, $quantity ) {
	if ( ! apex_is_media_package_product( $product_id ) ) {
		return $passed;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce verifies the add-to-cart request.
	$has_photo   = ! empty( $_FILES['apex_car_photo'] ) && ! empty( $_FILES['apex_car_photo']['tmp_name'] );
	$description = isset( $_POST['apex_car_description'] ) ? trim( wp_unslash( $_POST['apex_car_description'] ) ) : '';

	if ( ! $has_photo && '' === $description ) {
		wc_add_notice( __( 'Please upload a photo of your car or describe it so our team can find you on track.', 'apex-idaho' ), 'error' );
		return false;
	}

	if ( $has_photo ) {
		$file = $_FILES['apex_car_photo'];

		if ( UPLOAD_ERR_OK !== $file['error'] ) {
			wc_add_notice( __( 'There was a problem uploading your photo. Please try again or use the description field instead.', 'apex-idaho' ), 'error' );
			return false;
		}

		if ( $file['size'] > 10 * 1024 * 1024 ) {
			wc_add_notice( __( 'Your photo is larger than 10 MB. Please upload a smaller file.', 'apex-idaho' ), 'error' );
			return false;
		}

		$allowed = array( 'image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif' );
		$type    = wp_check_filetype( $file['name'] );
		$mime    = ! empty( $type['type'] ) ? $type['type'] : mime_content_type( $file['tmp_name'] );
		if ( ! in_array( $mime, $allowed, true ) ) {
			wc_add_notice( __( 'Photo must be a JPG, PNG, WEBP, or HEIC image.', 'apex-idaho' ), 'error' );
			return false;
		}
	}
	// phpcs:enable

	return $passed;
}

// Move the upload and stash both photo + description on the cart item.
add_filter( 'woocommerce_add_cart_item_data', 'apex_idaho_media_package_cart_item_data', 10, 2 );
function apex_idaho_media_package_cart_item_data( $cart_item_data, $product_id ) {
	if ( ! apex_is_media_package_product( $product_id ) ) {
		return $cart_item_data;
	}

	$stored_anything = false;

	// phpcs:disable WordPress.Security.NonceVerification.Missing
	if ( ! empty( $_FILES['apex_car_photo'] ) && ! empty( $_FILES['apex_car_photo']['tmp_name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$uploaded = wp_handle_upload( $_FILES['apex_car_photo'], array( 'test_form' => false ) );

		if ( ! empty( $uploaded['url'] ) && empty( $uploaded['error'] ) ) {
			$cart_item_data['apex_car_photo'] = array(
				'url'  => esc_url_raw( $uploaded['url'] ),
				'file' => $uploaded['file'],
				'name' => sanitize_file_name( basename( $uploaded['file'] ) ),
			);
			$stored_anything                  = true;
		}
	}

	if ( ! empty( $_POST['apex_car_description'] ) ) {
		$description = sanitize_textarea_field( wp_unslash( $_POST['apex_car_description'] ) );
		if ( '' !== $description ) {
			$cart_item_data['apex_car_description'] = $description;
			$stored_anything                        = true;
		}
	}
	// phpcs:enable

	if ( $stored_anything ) {
		// Unique key so each booking is its own cart line.
		$cart_item_data['unique_key'] = md5( microtime() . wp_rand() );
	}

	return $cart_item_data;
}

// Show the photo / description in cart and checkout.
add_filter( 'woocommerce_get_item_data', 'apex_idaho_media_package_item_data', 10, 2 );
function apex_idaho_media_package_item_data( $item_data, $cart_item ) {
	if ( ! empty( $cart_item['apex_car_photo']['url'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Car photo', 'apex-idaho' ),
			'value' => '<a href="' . esc_url( $cart_item['apex_car_photo']['url'] ) . '" target="_blank" rel="noopener">' . esc_html( $cart_item['apex_car_photo']['name'] ) . '</a>',
		);
	}
	if ( ! empty( $cart_item['apex_car_description'] ) ) {
		$item_data[] = array(
			'key'   => __( 'Car description', 'apex-idaho' ),
			'value' => esc_html( $cart_item['apex_car_description'] ),
		);
	}
	return $item_data;
}

// Persist both onto the order line item. The meta keys are the English labels,
// exactly as the child theme wrote them, so existing orders read the same.
add_action( 'woocommerce_checkout_create_order_line_item', 'apex_idaho_media_package_order_item_meta', 10, 4 );
function apex_idaho_media_package_order_item_meta( $item, $cart_item_key, $values, $order ) {
	if ( ! empty( $values['apex_car_photo']['url'] ) ) {
		$item->add_meta_data( 'Car photo', $values['apex_car_photo']['url'] );
	}
	if ( ! empty( $values['apex_car_description'] ) ) {
		$item->add_meta_data( 'Car description', $values['apex_car_description'] );
	}
}

// Render the "Car photo" meta value as a link in admin, cart, checkout and emails.
add_filter( 'woocommerce_order_item_display_meta_value', 'apex_idaho_media_package_meta_link', 10, 3 );
function apex_idaho_media_package_meta_link( $value, $meta, $item ) {
	if ( is_object( $meta ) && 'Car photo' === $meta->key && filter_var( $value, FILTER_VALIDATE_URL ) ) {
		return '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>';
	}
	return $value;
}

/**
 * Keep the header cart count (apex/cart-link) current after AJAX add-to-cart,
 * wherever WooCommerce's cart fragments script is running.
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'apex_idaho_cart_count_fragment' );
function apex_idaho_cart_count_fragment( $fragments ) {
	$count = ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;

	$fragments['span.apex-cart-link__count'] = sprintf(
		'<span class="apex-cart-link__count%1$s"><span class="screen-reader-text">%2$s</span>%3$d</span>',
		$count ? '' : ' is-zero',
		esc_html__( 'Items in cart:', 'apex-idaho' ),
		$count
	);

	return $fragments;
}
