<?php
/*
Plugin Name: Daily Sales Email
Description: Sends daily sales numbers for specified products to specified email addresses.
Version: 1.0.0
Author: Nic D. Ford
 */

// Ensure the file is being accessed via WordPress
if (!defined('ABSPATH')) {
    exit;
}

// Add a daily cron event on plugin activation
register_activation_hook(__FILE__, 'dse_activate');
function dse_activate()
{
    if (!wp_next_scheduled('dse_daily_sales_event')) {
        wp_schedule_event(time(), 'daily', 'dse_daily_sales_event');
    }
}

// Remove the daily cron event on plugin deactivation
register_deactivation_hook(__FILE__, 'dse_deactivate');
function dse_deactivate()
{
    wp_clear_scheduled_hook('dse_daily_sales_event');
}

// Hook the function to our custom event
add_action('dse_daily_sales_event', 'dse_send_daily_sales_email');

// Function to send the daily sales email
function dse_send_daily_sales_email()
{
    $product_ids = explode(',', get_option('dse_product_ids', ''));
    $email_addresses = explode(',', get_option('dse_email_addresses', ''));

    $sales_report = '';
    foreach ($product_ids as $product_id) {
        $product = wc_get_product($product_id);
        if ($product) {
            $sales = get_post_meta($product_id, 'total_sales', true);
            $sales_report .= "Product: " . $product->get_name() . "\nSales: " . $sales . "\n\n";
        }
    }

    $subject = 'Daily Sales Report';
    $message = $sales_report;

    if (empty($email_addresses)) {
        error_log('No email addresses specified for daily sales report');
        return;
    }

    foreach ($email_addresses as $email_address) {
        mail($email_address, $subject, $message);
    }
}

// Register settings for product IDs and email addresses in the admin panel
add_action('admin_menu', 'dse_create_menu');
function dse_create_menu()
{
    add_menu_page('Daily Sales Email Settings', 'Daily Sales Email', 'administrator', __FILE__, 'dse_settings_page', 'dashicons-email');
    add_action('admin_init', 'dse_register_settings');
}

function dse_register_settings()
{
    register_setting('dse-settings-group', 'dse_product_ids');
    register_setting('dse-settings-group', 'dse_email_addresses');
}

function dse_settings_page()
{
    ?>
<div class="wrap">
    <h1>Daily Sales Email Settings</h1>
    <form method="post" action="options.php">
        <?php settings_fields('dse-settings-group');?>
        <?php do_settings_sections('dse-settings-group');?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Product IDs (comma separated)</th>
                <td><input type="text" name="dse_product_ids" value="<?php echo esc_attr(get_option('dse_product_ids')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Email Addresses (comma separated)</th>
                <td><input type="text" name="dse_email_addresses" value="<?php echo esc_attr(get_option('dse_email_addresses')); ?>" /></td>
            </tr>
        </table>
        <?php submit_button();?>
    </form>
    <form method="post">
        <input type="hidden" name="dse_send_sales_report_now" value="1" />
        <?php submit_button('Send Sales Report Now');?>
    </form>
</div>
<?php
}

// Handle the button click to send the sales report immediately
add_action('admin_post_dse_send_sales_report_now', 'dse_handle_send_sales_report_now');
function dse_handle_send_sales_report_now()
{
    if (isset($_POST['dse_send_sales_report_now']) && $_POST['dse_send_sales_report_now'] == '1') {
        dse_send_daily_sales_email();
        wp_redirect(admin_url('admin.php?page=daily-sales-email%2Fdaily-sales-email.php&sent=1'));
        exit;
    }
}

// Add a notice to inform the admin that the report was sent
add_action('admin_notices', 'dse_admin_notice');
function dse_admin_notice()
{
    if (isset($_GET['sent']) && $_GET['sent'] == '1') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Sales report sent successfully!', 'dse-text-domain');?></p>
        </div>
        <?php
}
}
?>
