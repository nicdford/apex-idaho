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

add_action('add_meta_boxes', 'apex_sponsor_preview_meta_box');
function apex_sponsor_preview_meta_box()
{
    add_meta_box(
        'sponsor_logo_preview',
        'Logo Preview',
        'apex_render_sponsor_preview',
        'sponsor',
        'side',
        'high'
    );
}

function apex_render_sponsor_preview($post)
{
    $logo_id  = carbon_get_post_meta($post->ID, 'sponsor_logo');
    $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';

    if (!$logo_url) {
        echo '<p style="color:#999;margin:0;">No logo uploaded yet.</p>';
        return;
    }

    $to_black = carbon_get_post_meta($post->ID, 'sponsor_to_black');
    $invert   = carbon_get_post_meta($post->ID, 'sponsor_invert');

    $filters = array_filter([
        $to_black === 'grayscale'  ? 'grayscale(1)'  : '',
        $to_black === 'brightness' ? 'brightness(0)' : '',
        $invert                    ? 'invert(1)'     : '',
    ]);
    $filter_str = $filters ? 'filter:' . implode(' ', $filters) . ';' : '';

    echo '<div style="background:#e0e0e0;padding:1rem;text-align:center;border-radius:2px;">';
    echo '<img src="' . esc_url($logo_url) . '" style="max-width:100%;max-height:80px;' . esc_attr($filter_str) . '">';
    echo '</div>';
    echo '<p style="color:#999;font-size:11px;margin:6px 0 0;">Save to refresh preview.</p>';
}

add_action('carbon_fields_register_fields', 'apex_register_sponsor_fields');
function apex_register_sponsor_fields()
{
    Container::make('post_meta', 'Sponsor Details')
        ->where('post_type', '=', 'sponsor')
        ->add_fields([
            Field::make('image', 'sponsor_logo', 'Logo'),
            Field::make('select', 'sponsor_to_black', 'Convert to black')
                ->add_options([
                    ''           => '— None —',
                    'grayscale'  => 'Grayscale',
                    'brightness' => 'Brightness',
                ]),
            Field::make('checkbox', 'sponsor_invert', 'Invert colors')
                ->set_help_text('Inverts all colors (invert(1)).'),
            Field::make('text', 'sponsor_url', 'Website URL')
                ->set_attribute('placeholder', 'https://'),
        ]);
}

// Focal point picker for media_partner featured image
add_action('add_meta_boxes', function () {
    add_meta_box(
        'apex_focal_point',
        'Photo Focal Point',
        'apex_render_focal_meta_box',
        'media_partner',
        'side',
        'default'
    );
});

function apex_render_focal_meta_box($post)
{
    wp_nonce_field('apex_focal_save', 'apex_focal_nonce');
    $thumb_id = get_post_thumbnail_id($post->ID);
    if (!$thumb_id) {
        echo '<p style="color:#999;margin:0;">Set a featured image first, then save and return here.</p>';
        return;
    }
    $img_url = wp_get_attachment_image_url($thumb_id, 'medium_large');
    $x = get_post_meta($post->ID, '_apex_focal_x', true);
    $y = get_post_meta($post->ID, '_apex_focal_y', true);
    if ($x === '') $x = 50;
    if ($y === '') $y = 25;
    ?>
    <p style="margin:0 0 8px;color:#666;font-size:12px;">Click on the image to set the focal point — this is the spot kept centered when the photo is cropped.</p>
    <div class="apex-focal-wrap" style="position:relative;display:inline-block;max-width:100%;cursor:crosshair;user-select:none;line-height:0;">
        <img src="<?php echo esc_url($img_url); ?>" style="display:block;max-width:100%;height:auto;" id="apex-focal-img" alt="">
        <div id="apex-focal-marker" style="position:absolute;width:18px;height:18px;border:2px solid #fff;border-radius:50%;box-shadow:0 0 0 2px #e8197d, 0 0 6px rgba(0,0,0,.6);transform:translate(-50%,-50%);pointer-events:none;left:<?php echo esc_attr($x); ?>%;top:<?php echo esc_attr($y); ?>%;"></div>
    </div>
    <p style="margin:8px 0 0;font-size:12px;color:#666;">
        X: <span id="apex-focal-x-disp"><?php echo esc_html($x); ?></span>%
        &nbsp; Y: <span id="apex-focal-y-disp"><?php echo esc_html($y); ?></span>%
    </p>
    <input type="hidden" name="apex_focal_x" id="apex-focal-x" value="<?php echo esc_attr($x); ?>">
    <input type="hidden" name="apex_focal_y" id="apex-focal-y" value="<?php echo esc_attr($y); ?>">
    <script>
    (function () {
        var wrap = document.querySelector('.apex-focal-wrap');
        if (!wrap) return;
        var img = document.getElementById('apex-focal-img');
        var marker = document.getElementById('apex-focal-marker');
        var inX = document.getElementById('apex-focal-x');
        var inY = document.getElementById('apex-focal-y');
        var dispX = document.getElementById('apex-focal-x-disp');
        var dispY = document.getElementById('apex-focal-y-disp');
        function setFocal(e) {
            var r = img.getBoundingClientRect();
            var x = Math.max(0, Math.min(100, ((e.clientX - r.left) / r.width) * 100));
            var y = Math.max(0, Math.min(100, ((e.clientY - r.top) / r.height) * 100));
            x = Math.round(x); y = Math.round(y);
            marker.style.left = x + '%';
            marker.style.top = y + '%';
            inX.value = x; inY.value = y;
            dispX.textContent = x; dispY.textContent = y;
        }
        wrap.addEventListener('click', setFocal);
    })();
    </script>
    <?php
}

add_action('save_post_media_partner', function ($post_id) {
    if (!isset($_POST['apex_focal_nonce']) || !wp_verify_nonce($_POST['apex_focal_nonce'], 'apex_focal_save')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['apex_focal_x'])) {
        update_post_meta($post_id, '_apex_focal_x', max(0, min(100, intval($_POST['apex_focal_x']))));
    }
    if (isset($_POST['apex_focal_y'])) {
        update_post_meta($post_id, '_apex_focal_y', max(0, min(100, intval($_POST['apex_focal_y']))));
    }
});

/* ============================================================
 * Media Package — require car photo upload on add-to-cart
 * ============================================================ */

define('APEX_MEDIA_PACKAGE_PRODUCT_ID', 2825);

function apex_is_media_package_product($product_id) {
    return intval($product_id) === APEX_MEDIA_PACKAGE_PRODUCT_ID;
}

// Make the single-product form support file uploads
add_filter('woocommerce_product_single_add_to_cart_text', function ($text) { return $text; });
add_action('woocommerce_before_single_product', function () {
    global $product;
    if (!$product || !apex_is_media_package_product($product->get_id())) return;
    add_action('woocommerce_before_add_to_cart_form', function () {
        echo '<script>document.addEventListener("DOMContentLoaded",function(){var f=document.querySelector("form.cart");if(f){f.setAttribute("enctype","multipart/form-data");}});</script>';
    });
});

// Render the upload + description fields on the Media Package product page
add_action('woocommerce_before_add_to_cart_button', function () {
    global $product;
    if (!$product || !apex_is_media_package_product($product->get_id())) return;
    ?>
    <div class="apex-car-photo-field" style="margin:1.25rem 0;padding:1rem;border:1px solid #e8197d;background:#fff5fa;">
        <p style="font-weight:600;margin:0 0 0.75rem;">
            Help our team spot your car on track <span style="color:#e8197d;">*</span>
        </p>
        <p style="font-size:0.9rem;color:#555;margin:0 0 1rem;">
            Upload a photo <strong>or</strong> describe your car below — at least one is required.
        </p>

        <label for="apex_car_photo" style="display:block;font-weight:600;margin-bottom:0.4rem;font-size:0.95rem;">
            Photo of your car
        </label>
        <p style="font-size:0.85rem;color:#666;margin:0 0 0.4rem;">JPG, PNG, HEIC, or WEBP. Max 10 MB.</p>
        <input type="file" id="apex_car_photo" name="apex_car_photo"
               accept="image/jpeg,image/png,image/webp,image/heic,image/heif"
               style="margin-bottom:1rem;">

        <label for="apex_car_description" style="display:block;font-weight:600;margin-bottom:0.4rem;font-size:0.95rem;">
            Or describe your car
        </label>
        <p style="font-size:0.85rem;color:#666;margin:0 0 0.4rem;">e.g. "Red 2018 Mustang GT, black wheels, #42 on driver door"</p>
        <textarea id="apex_car_description" name="apex_car_description" rows="3"
                  maxlength="500" style="width:100%;padding:0.5rem;"></textarea>
    </div>
    <?php
});

// Validate: require photo OR description
add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id, $quantity) {
    if (!apex_is_media_package_product($product_id)) return $passed;

    $has_photo = !empty($_FILES['apex_car_photo']) && !empty($_FILES['apex_car_photo']['tmp_name']);
    $description = isset($_POST['apex_car_description']) ? trim(wp_unslash($_POST['apex_car_description'])) : '';

    if (!$has_photo && $description === '') {
        wc_add_notice(__('Please upload a photo of your car or describe it so our team can find you on track.'), 'error');
        return false;
    }

    if ($has_photo) {
        $file = $_FILES['apex_car_photo'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            wc_add_notice(__('There was a problem uploading your photo. Please try again or use the description field instead.'), 'error');
            return false;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            wc_add_notice(__('Your photo is larger than 10 MB. Please upload a smaller file.'), 'error');
            return false;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/heic', 'image/heif'];
        $type = wp_check_filetype($file['name']);
        $mime = !empty($type['type']) ? $type['type'] : mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowed, true)) {
            wc_add_notice(__('Photo must be a JPG, PNG, WEBP, or HEIC image.'), 'error');
            return false;
        }
    }

    return $passed;
}, 10, 3);

// Move the upload and stash both photo + description on the cart item
add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id) {
    if (!apex_is_media_package_product($product_id)) return $cart_item_data;

    $stored_anything = false;

    if (!empty($_FILES['apex_car_photo']) && !empty($_FILES['apex_car_photo']['tmp_name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $uploaded = wp_handle_upload($_FILES['apex_car_photo'], ['test_form' => false]);

        if (!empty($uploaded['url']) && empty($uploaded['error'])) {
            $cart_item_data['apex_car_photo'] = [
                'url'  => esc_url_raw($uploaded['url']),
                'file' => $uploaded['file'],
                'name' => sanitize_file_name(basename($uploaded['file'])),
            ];
            $stored_anything = true;
        }
    }

    if (!empty($_POST['apex_car_description'])) {
        $description = sanitize_textarea_field(wp_unslash($_POST['apex_car_description']));
        if ($description !== '') {
            $cart_item_data['apex_car_description'] = $description;
            $stored_anything = true;
        }
    }

    if ($stored_anything) {
        // Unique key so each booking is its own cart line
        $cart_item_data['unique_key'] = md5(microtime() . wp_rand());
    }

    return $cart_item_data;
}, 10, 2);

// Display the photo / description in cart/checkout
add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {
    if (!empty($cart_item['apex_car_photo']['url'])) {
        $item_data[] = [
            'key'   => __('Car photo'),
            'value' => '<a href="' . esc_url($cart_item['apex_car_photo']['url']) . '" target="_blank" rel="noopener">' . esc_html($cart_item['apex_car_photo']['name']) . '</a>',
        ];
    }
    if (!empty($cart_item['apex_car_description'])) {
        $item_data[] = [
            'key'   => __('Car description'),
            'value' => esc_html($cart_item['apex_car_description']),
        ];
    }
    return $item_data;
}, 10, 2);

// Persist both onto the order line item
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
    if (!empty($values['apex_car_photo']['url'])) {
        $item->add_meta_data(__('Car photo'), $values['apex_car_photo']['url']);
    }
    if (!empty($values['apex_car_description'])) {
        $item->add_meta_data(__('Car description'), $values['apex_car_description']);
    }
}, 10, 4);

// Render the "Car photo" meta value as a clickable link in admin, cart, checkout, and emails
add_filter('woocommerce_order_item_display_meta_value', function ($value, $meta, $item) {
    if (is_object($meta) && $meta->key === 'Car photo' && filter_var($value, FILTER_VALIDATE_URL)) {
        return '<a href="' . esc_url($value) . '" target="_blank" rel="noopener">' . esc_html($value) . '</a>';
    }
    return $value;
}, 10, 3);

// WP core's iframe_header() dispatches admin_enqueue_scripts with a null hook, which
// crashes any callback that types its first arg as `string` (no `?string`). Examples
// seen: Event Tickets' vendored Harbor Feature_Manager_Page. Wrap such callbacks so
// null becomes "" — keeps third-party plugins working without patching vendor code.
add_action('admin_init', function () {
    global $wp_filter;
    if (empty($wp_filter['admin_enqueue_scripts'])) {
        return;
    }
    foreach ($wp_filter['admin_enqueue_scripts']->callbacks as $priority => $callbacks) {
        foreach ($callbacks as $id => $cb) {
            $fn = $cb['function'];
            try {
                $ref = is_array($fn)
                    ? new ReflectionMethod($fn[0], $fn[1])
                    : (is_string($fn) || $fn instanceof Closure ? new ReflectionFunction($fn) : null);
            } catch (Throwable $e) {
                continue;
            }
            if (!$ref) {
                continue;
            }
            $params = $ref->getParameters();
            if (!$params) {
                continue;
            }
            $type = $params[0]->getType();
            if (!$type instanceof ReflectionNamedType) {
                continue;
            }
            if ($type->allowsNull() || $type->getName() !== 'string') {
                continue;
            }
            $wp_filter['admin_enqueue_scripts']->callbacks[$priority][$id]['function'] = function ($hook = null) use ($fn) {
                return call_user_func($fn, $hook ?? '');
            };
        }
    }
}, 1);
