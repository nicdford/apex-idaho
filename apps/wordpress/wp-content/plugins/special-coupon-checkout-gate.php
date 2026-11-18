<?php
/*
Plugin Name: Special Coupon Checkout Gate
Description: Blocks checkout for gated WooCommerce products unless a specific coupon is applied to the cart. The coupon does not discount anything; it only unlocks checkout.
Version: 1.0.0
Requires Plugins: woocommerce
*/

if (!defined('ABSPATH')) {
    exit;
}

class Special_Coupon_Checkout_Gate {

    // Coupon code (lowercase, as WooCommerce stores it) that unlocks checkout.
    const COUPON_CODE = 'specialpeeps';

    // Products that cannot be checked out without the coupon.
    const GATED_PRODUCT_IDS = [3288];

    const GATE_MESSAGE = 'This ticket requires a valid access code. Apply your code to continue to checkout.';

    public function __construct() {
        add_action('woocommerce_check_cart_items', [$this, 'add_cart_notice']);
        add_action('woocommerce_after_checkout_validation', [$this, 'block_checkout'], 10, 2);
    }

    private function cart_has_gated_product(): bool {
        if (!WC()->cart) {
            return false;
        }
        foreach (WC()->cart->get_cart() as $item) {
            if (in_array((int) $item['product_id'], self::GATED_PRODUCT_IDS, true)) {
                return true;
            }
        }
        return false;
    }

    private function gate_is_unlocked(): bool {
        return WC()->cart && WC()->cart->has_discount(self::COUPON_CODE);
    }

    public function add_cart_notice() {
        if ($this->cart_has_gated_product() && !$this->gate_is_unlocked()) {
            wc_add_notice(self::GATE_MESSAGE, 'notice');
        }
    }

    public function block_checkout($data, $errors) {
        if ($this->cart_has_gated_product() && !$this->gate_is_unlocked()) {
            $errors->add('special_coupon_required', self::GATE_MESSAGE);
        }
    }
}

new Special_Coupon_Checkout_Gate();
