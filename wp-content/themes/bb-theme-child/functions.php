<?php

// Defines
define('FL_CHILD_THEME_DIR', get_stylesheet_directory());
define('FL_CHILD_THEME_URL', get_stylesheet_directory_uri());

// Carbon Fields
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action('wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000);

// Force 16px root font size — overrides BB theme's html { font-size: 10px }
// Added to both wp_head and wp_footer to catch BB plugin's cached CSS loaded in footer
add_action('wp_head', function() {
    echo '<style>html { font-size: 16px !important; }</style>';
}, 999);
add_action('wp_footer', function() {
    echo '<style>html { font-size: 16px !important; }</style>';
}, 999);

/**
 * Auto Complete all WooCommerce orders.
 */
add_action('woocommerce_thankyou', 'custom_woocommerce_auto_complete_order');

function custom_woocommerce_auto_complete_order($order_id)
{
  if (!$order_id) {
    return;
  }

  $order = wc_get_order($order_id);
  $order->update_status('completed');
}

/**
 * Require coupon for checkout on grass valley driver entry tickets
 */
// add_action('woocommerce_check_cart_items', 'mandatory_coupon_for_grass_valley_fos');
function mandatory_coupon_for_grass_valley_fos()
{
  $targeted_ids = array(807); // The targeted product ids (in this array)
  $coupon_codes = ['STUZSBWX', 'URCHQDGD', 'T338RSPY', '86JZ63RR', 'AJMSDUM2', '9YYQ9JU2', 'SA79P45V']; // The required coupon codes

  $applied_coupons = WC()->cart->get_applied_coupons();

  // Check if any of the coupon codes are applied
  $valid_coupon_applied = false;
  foreach ($coupon_codes as $code) {
    if (in_array(strtolower($code), $applied_coupons)) {
      $valid_coupon_applied = true;
      break;
    }
  }

  // Loop through cart items
  foreach (WC()->cart->get_cart() as $cart_item) {
    // Check cart item for defined product Ids and applied coupon
    if (in_array($cart_item['product_id'], $targeted_ids) && !$valid_coupon_applied) {
      wc_clear_notices(); // Clear all other notices

      // Avoid checkout displaying an error notice
      // "GVFOS24 Driver Entry requires a coupon code for checkout. Open registration will begin shortly."
      wc_add_notice(sprintf('%s requires a coupon code for checkout. Open registration will begin shortly.', $cart_item['data']->get_name()), 'error');
      break; // stop the loop
    }
  }
}

/**
 * Require coupon for test product
 */
// add_action('woocommerce_check_cart_items', 'mandatory_coupon_for_test_product');
function mandatory_coupon_for_test_product()
{
  $targeted_ids = array(818); // The targeted product ids (in this array)
  $coupon_codes = ['TESTCOUPON']; // The required coupon codes

  $applied_coupons = WC()->cart->get_applied_coupons();

  // Check if any of the coupon codes are applied
  $valid_coupon_applied = false;
  foreach ($coupon_codes as $code) {
    if (in_array(strtolower($code), $applied_coupons)) {
      $valid_coupon_applied = true;
      break;
    }
  }

  // Loop through cart items
  foreach (WC()->cart->get_cart() as $cart_item) {
    // Check cart item for defined product Ids and applied coupon
    if (in_array($cart_item['product_id'], $targeted_ids) && !$valid_coupon_applied) {
      wc_clear_notices(); // Clear all other notices

      // Avoid checkout displaying an error notice
      wc_add_notice(sprintf('The product "%s" requires a coupon for checkout.', $cart_item['data']->get_name()), 'error');
      break; // stop the loop
    }
  }
}

function get_order_by_id($order_id)
{
  if (class_exists('WooCommerce')) {
    return wc_get_order($order_id);
  }
}

function order_has_coupon($coupon, $order)
{
  return in_array($coupon, $order->get_coupon_codes());
}

/**
 * Sponsor Custom Post Type
 */
add_action('init', 'register_sponsor_post_type');
function register_sponsor_post_type()
{
    register_post_type('sponsor', [
        'labels' => [
            'name'          => 'Sponsors',
            'singular_name' => 'Sponsor',
            'add_new_item'  => 'Add New Sponsor',
            'edit_item'     => 'Edit Sponsor',
            'all_items'     => 'All Sponsors',
        ],
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'supports'     => ['title'],
        'menu_icon'    => 'dashicons-star-filled',
    ]);
}

/**
 * Carbon Fields setup
 */
add_action('after_setup_theme', 'apex_boot_carbon_fields');
function apex_boot_carbon_fields()
{
    if ( class_exists( '\Carbon_Fields\Carbon_Fields' ) ) {
        \Carbon_Fields\Carbon_Fields::boot();
    }
}

add_action('carbon_fields_register_fields', 'apex_register_sponsor_fields');
function apex_register_sponsor_fields()
{
    Container::make('post_meta', 'Sponsor Details')
        ->where('post_type', '=', 'sponsor')
        ->add_fields([
            Field::make('image', 'sponsor_logo', 'Logo'),
            Field::make('checkbox', 'sponsor_to_black', 'Convert to black')
                ->set_help_text('Desaturates the logo to grayscale (grayscale(1)).'),
            Field::make('checkbox', 'sponsor_invert', 'Invert colors')
                ->set_help_text('Inverts all colors (invert(1)). Useful for dark logos on light backgrounds.'),
            Field::make('text', 'sponsor_brightness', 'Brightness')
                ->set_attribute('type', 'number')
                ->set_attribute('step', '0.05')
                ->set_attribute('min', '0')
                ->set_attribute('max', '3')
                ->set_help_text('Adjust brightness. 0 = black, 1 = normal, 2 = double. Leave empty to skip.'),
            Field::make('text', 'sponsor_url', 'Website URL')
                ->set_attribute('placeholder', 'https://'),
        ]);
}
