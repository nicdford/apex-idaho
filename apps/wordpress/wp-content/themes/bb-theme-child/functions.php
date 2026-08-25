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

/* ============================================================
 * Luau Party landing template
 * ============================================================ */

/**
 * Post 2749 is a tribe_events single, not a page, so there is no per-ID slot in
 * the template hierarchy for it — page-{ID}.php only ever applies to the `page`
 * post type. Swapping the template on `template_include` is the supported route
 * for a single CPT post.
 *
 * Priority 9999 matters: both The Events Calendar and Beaver Themer set the
 * template for event singles (this event currently renders through a Themer
 * layout), and this has to run after them to win.
 */
define( 'APEX_LUAU_POST_ID', 2749 );

add_filter( 'template_include', 'apex_luau_template_include', 9999 );
function apex_luau_template_include( $template )
{
    if ( ! is_singular() ) {
        return $template;
    }

    $target = (int) apply_filters( 'apex_luau_post_id', APEX_LUAU_POST_ID );
    if ( (int) get_queried_object_id() !== $target ) {
        return $template;
    }

    $custom = locate_template( 'template-luau-party.php' );

    // Fall through to the normal event template if the file is ever missing,
    // rather than fataling on a live ticket-selling page.
    return $custom ? $custom : $template;
}

/**
 * Luau callout band.
 *
 * The homepage is a Beaver Builder layout stored in the database, so there is
 * no template to edit. This registers the callout two ways: a [luau_callout]
 * shortcode that can be dropped into any BB layout, and an automatic injection
 * at the top of the front page.
 *
 * It renders nothing at all when the event is missing, unpublished, or already
 * over — a stale event promo on the homepage is worse than none.
 */
/**
 * Renders "September 5-6", "September 5", or "September 30 - October 1".
 *
 * Lives here rather than in template-luau-party.php because the homepage
 * callout needs it too and that template is not loaded on the front page.
 * The template keeps its own function_exists-guarded copy, which simply does
 * not redefine when this one is already present.
 */
if ( ! function_exists( 'apex_luau_format_date_range' ) ) {
    function apex_luau_format_date_range( $start, $end )
    {
        $s = $start ? strtotime( $start ) : 0;
        if ( ! $s ) {
            return '';
        }
        $e = $end ? strtotime( $end ) : $s;

        if ( gmdate( 'Y-m-d', $s ) === gmdate( 'Y-m-d', $e ) ) {
            return date_i18n( 'F j', $s );
        }
        if ( gmdate( 'Y-n', $s ) === gmdate( 'Y-n', $e ) ) {
            return date_i18n( 'F j', $s ) . '–' . date_i18n( 'j', $e );
        }
        return date_i18n( 'F j', $s ) . ' – ' . date_i18n( 'F j', $e );
    }
}

function apex_luau_callout_should_render()
{
    $id   = (int) apply_filters( 'apex_luau_post_id', APEX_LUAU_POST_ID );
    $post = get_post( $id );

    if ( ! $post || 'publish' !== $post->post_status ) {
        return false;
    }

    if ( function_exists( 'tribe_get_end_date' ) && 'tribe_events' === get_post_type( $id ) ) {
        $end = tribe_get_end_date( $id, false, 'Y-m-d H:i:s' );
        if ( $end && strtotime( $end ) < current_time( 'timestamp' ) ) {
            return false;
        }
    }

    return $id;
}

function apex_luau_callout_html()
{
    $id = apex_luau_callout_should_render();
    if ( ! $id ) {
        return '';
    }

    $start = '';
    $end   = '';
    if ( function_exists( 'tribe_get_start_date' ) && 'tribe_events' === get_post_type( $id ) ) {
        $start = tribe_get_start_date( $id, false, 'Y-m-d H:i:s' );
        $end   = tribe_get_end_date( $id, false, 'Y-m-d H:i:s' );
    }

    // Reuse the landing page's date formatter when it is loaded; otherwise fall
    // back to a plain format so the callout never depends on that template.
    if ( $start && function_exists( 'apex_luau_format_date_range' ) ) {
        $dates = apex_luau_format_date_range( $start, $end );
    } elseif ( $start ) {
        $dates = date_i18n( 'F j', strtotime( $start ) );
    } else {
        $dates = '';
    }

    $venue = function_exists( 'tribe_get_venue' ) ? tribe_get_venue( $id ) : '';
    if ( ! $venue ) {
        $venue = 'Magic Valley Speedway';
    }

    $target = 0;
    if ( $start ) {
        try {
            $dt     = new DateTime( $start, wp_timezone() );
            $target = $dt->getTimestamp() * 1000;
        } catch ( Exception $e ) {
            $target = 0;
        }
    }

    ob_start();
    ?>
    <style>
    /* Self-contained: the landing page's stylesheet is not loaded here. */
    .luau-callout {
      --lc-magenta: #ff2bd6;
      --lc-lime:    #7bff4d;
      --lc-violet:  #8b3cff;
      position: relative;
      isolation: isolate;
      overflow: hidden;
      background: #05000c;
      padding: clamp(2.2rem, 5vw, 3.6rem) clamp(1.25rem, 5vw, 3rem);
      font-family: 'Barlow Condensed', system-ui, sans-serif;
    }
    .luau-callout *, .luau-callout *::before, .luau-callout *::after { box-sizing: border-box; }
    .luau-callout__glow {
      position: absolute; inset: 0; z-index: 0; pointer-events: none;
      background:
        radial-gradient(60% 120% at 12% 50%, rgba(255,43,214,.42), transparent 70%),
        radial-gradient(55% 120% at 88% 50%, rgba(139,60,255,.40), transparent 70%);
    }
    .luau-callout__inner {
      position: relative; z-index: 1;
      max-width: 1180px; margin: 0 auto;
      display: flex; flex-wrap: wrap; align-items: center;
      justify-content: space-between; gap: 1.5rem 2rem;
    }
    .luau-callout__eyebrow {
      margin: 0 0 .35rem;
      font-weight: 700; letter-spacing: .34em; text-transform: uppercase;
      font-size: .72rem; color: rgba(255,255,255,.72);
    }
    .luau-callout__title {
      margin: 0; line-height: .9;
      font-family: 'Archivo Black', Impact, sans-serif;
      text-transform: uppercase;
      font-size: clamp(2.4rem, 6vw, 4rem);
      color: #fff;
      text-shadow: 0 0 4px #fff, 0 0 14px var(--lc-magenta), 0 0 38px rgba(255,43,214,.6);
    }
    .luau-callout__script {
      font-family: 'Dancing Script', cursive; font-weight: 700;
      text-transform: none; font-size: .82em; color: #d9ffcc;
      text-shadow: 0 0 4px #eaffe2, 0 0 14px var(--lc-lime), 0 0 34px rgba(123,255,77,.5);
    }
    .luau-callout__meta {
      margin: .7rem 0 0;
      font-weight: 700; letter-spacing: .12em; text-transform: uppercase;
      font-size: clamp(.92rem, 2vw, 1.12rem); color: rgba(255,255,255,.86);
    }
    .luau-callout__meta span { color: var(--lc-magenta); }
    .luau-callout__side { display: flex; align-items: center; flex-wrap: wrap; gap: 1rem 1.4rem; }
    .luau-callout__countdown { display: flex; gap: .55rem; }
    .luau-callout__countdown span {
      display: grid; justify-items: center; min-width: 62px;
      padding: .5rem .4rem .4rem;
      border: 1px solid rgba(255,43,214,.5); border-radius: 12px;
      background: rgba(255,43,214,.08);
      font-size: .62rem; font-weight: 700; letter-spacing: .18em;
      text-transform: uppercase; color: rgba(255,255,255,.6);
    }
    .luau-callout__countdown b {
      font-family: 'Archivo Black', Impact, sans-serif;
      font-size: 1.35rem; line-height: 1; color: #fff;
      font-variant-numeric: tabular-nums;
      text-shadow: 0 0 10px var(--lc-magenta);
    }
    .luau-callout__btn {
      display: inline-block; padding: .95rem 2.1rem;
      border-radius: 999px; border: 0; text-decoration: none;
      background: linear-gradient(115deg, var(--lc-magenta) 0%, #ff7ae3 46%, var(--lc-lime) 100%);
      color: #14000d !important;
      font-weight: 700; font-size: 1.02rem; letter-spacing: .16em; text-transform: uppercase;
      box-shadow: 0 0 24px rgba(255,43,214,.45);
      transition: transform .25s ease, box-shadow .25s ease;
    }
    .luau-callout__btn:hover, .luau-callout__btn:focus-visible {
      transform: translateY(-2px);
      box-shadow: 0 0 38px rgba(255,43,214,.75);
      color: #14000d !important;
    }
    @media (max-width: 720px) {
      /* The side column has to stretch too, or width:100% on the button just
         resolves against a shrink-to-fit parent and nothing spans. */
      .luau-callout__inner { flex-direction: column; align-items: stretch; }
      .luau-callout__side  { width: 100%; }
      .luau-callout__btn   { width: 100%; text-align: center; }
    }
    @media (prefers-reduced-motion: reduce) {
      .luau-callout__btn { transition: none; }
    }
    </style>
    <aside class="luau-callout">
      <div class="luau-callout__glow" aria-hidden="true"></div>
      <div class="luau-callout__inner">
        <div class="luau-callout__lockup">
          <p class="luau-callout__eyebrow">APEX Idaho Presents</p>
          <h2 class="luau-callout__title">
            Luau <span class="luau-callout__script">Party</span>
          </h2>
          <p class="luau-callout__meta">
            <?php echo esc_html( $dates ); ?>
            <span aria-hidden="true">&nbsp;&#9670;&nbsp;</span>
            <?php echo esc_html( $venue ); ?>
          </p>
        </div>

        <div class="luau-callout__side">
          <?php if ( $target ) : ?>
          <div class="luau-callout__countdown" data-luau-callout-target="<?php echo esc_attr( $target ); ?>" role="timer">
            <span><b data-d>--</b>days</span>
            <span><b data-h>--</b>hrs</span>
            <span><b data-m>--</b>min</span>
          </div>
          <?php endif; ?>
          <a class="luau-callout__btn" href="<?php echo esc_url( get_permalink( $id ) ); ?>">Get Tickets</a>
        </div>
      </div>
    </aside>
    <script>
    (function () {
      var el = document.currentScript.previousElementSibling.querySelector('[data-luau-callout-target]');
      if (!el) { return; }
      var target = parseInt(el.getAttribute('data-luau-callout-target'), 10);
      var d = el.querySelector('[data-d]'), h = el.querySelector('[data-h]'), m = el.querySelector('[data-m]');
      var pad = function (n) { return n < 10 ? '0' + n : String(n); };
      var tick = function () {
        var diff = target - Date.now();
        if (diff <= 0) { d.textContent = 'NOW'; h.parentNode.style.display = 'none'; m.parentNode.style.display = 'none'; return true; }
        var s = Math.floor(diff / 1000);
        d.textContent = Math.floor(s / 86400);
        h.textContent = pad(Math.floor(s / 3600) % 24);
        m.textContent = pad(Math.floor(s / 60) % 60);
        return false;
      };
      if (!tick()) { var t = setInterval(function () { if (tick()) { clearInterval(t); } }, 30000); }
    })();
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode( 'luau_callout', 'apex_luau_callout_html' );

/**
 * Assets load only on requests that will actually show the callout.
 */
add_action( 'wp_enqueue_scripts', 'apex_luau_callout_assets' );
function apex_luau_callout_assets()
{
    if ( ! is_front_page() || ! apex_luau_callout_should_render() ) {
        return;
    }

    wp_enqueue_style(
        'apex-luau-callout-fonts',
        'https://fonts.googleapis.com/css2?family=Archivo+Black&family=Barlow+Condensed:wght@400;600;700&family=Dancing+Script:wght@700&display=swap',
        array(),
        null
    );
}

/**
 * Prepend to the front page. Return the content untouched anywhere else, and
 * only act on the main query in the loop so widgets and excerpts are unaffected.
 */
// Priority 20, not 5: Beaver Builder filters the_content at 10 and REPLACES it
// with the rendered layout, so anything prepended earlier is discarded.
add_filter( 'the_content', 'apex_luau_callout_on_front_page', 20 );
function apex_luau_callout_on_front_page( $content )
{
    if ( is_admin() || ! is_front_page() || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    if ( ! apply_filters( 'apex_luau_callout_auto', true ) ) {
        return $content;
    }

    $callout = apex_luau_callout_html();

    return $callout ? $callout . $content : $content;
}
