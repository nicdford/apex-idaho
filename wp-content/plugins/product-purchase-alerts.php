<?php
/*
Plugin Name: Product Purchase Alerts
Description: Send email notifications to specific people when specific WooCommerce products are purchased. Supports multiple alerts with different products and recipients.
Version: 1.0.0
Author: Nic D. Ford
Author URI: https://nicdford.com
Requires Plugins: woocommerce
*/

if (!defined('ABSPATH')) {
    exit;
}

class Product_Purchase_Alerts {

    const OPTION_KEY = 'ppa_alerts';

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_init', [$this, 'handle_form_submission']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('woocommerce_order_status_completed', [$this, 'check_order_for_alerts']);
        add_action('woocommerce_payment_complete', [$this, 'check_order_for_alerts']);
    }

    /**
     * Get all saved alerts.
     */
    public static function get_alerts(): array {
        return get_option(self::OPTION_KEY, []);
    }

    /**
     * Admin menu under WooCommerce.
     */
    public function add_menu_page(): void {
        add_submenu_page(
            'woocommerce',
            'Purchase Alerts',
            'Purchase Alerts',
            'manage_woocommerce',
            'product-purchase-alerts',
            [$this, 'render_settings_page']
        );
    }

    /**
     * Enqueue Select2 for the product dropdown (WooCommerce ships it).
     */
    public function enqueue_admin_assets(string $hook): void {
        if ($hook !== 'woocommerce_page_product-purchase-alerts') {
            return;
        }

        wp_enqueue_style('ppa-admin', false);
        wp_enqueue_script('select2');
        wp_enqueue_style('select2');

        wp_add_inline_style('ppa-admin', '
            .ppa-alerts-table { width: 100%; border-collapse: collapse; margin-top: 12px; }
            .ppa-alerts-table th { text-align: left; padding: 10px 12px; border-bottom: 2px solid #c3c4c7; }
            .ppa-alerts-table td { padding: 10px 12px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
            .ppa-alerts-table tr:hover td { background: #f6f7f7; }
            .ppa-alert-form { background: #fff; border: 1px solid #c3c4c7; padding: 20px; margin-top: 16px; max-width: 680px; }
            .ppa-alert-form label { display: block; font-weight: 600; margin-bottom: 4px; }
            .ppa-alert-form .field { margin-bottom: 16px; }
            .ppa-alert-form input[type="text"],
            .ppa-alert-form textarea,
            .ppa-alert-form select { width: 100%; }
            .ppa-alert-form textarea { min-height: 80px; }
            .ppa-status-enabled { color: #00a32a; font-weight: 600; }
            .ppa-status-disabled { color: #b32d2e; }
            .ppa-badge { display: inline-block; background: #f0f0f1; border-radius: 3px; padding: 2px 8px; margin: 2px 3px 2px 0; font-size: 12px; }
        ');
    }

    /**
     * Handle add / edit / delete form submissions.
     */
    public function handle_form_submission(): void {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }

        // Delete alert
        if (isset($_GET['ppa_delete'], $_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ppa_delete')) {
            $alerts = self::get_alerts();
            $index = absint($_GET['ppa_delete']);
            if (isset($alerts[$index])) {
                unset($alerts[$index]);
                update_option(self::OPTION_KEY, array_values($alerts));
            }
            wp_safe_redirect(admin_url('admin.php?page=product-purchase-alerts&ppa_msg=deleted'));
            exit;
        }

        // Toggle alert
        if (isset($_GET['ppa_toggle'], $_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'ppa_toggle')) {
            $alerts = self::get_alerts();
            $index = absint($_GET['ppa_toggle']);
            if (isset($alerts[$index])) {
                $alerts[$index]['enabled'] = !$alerts[$index]['enabled'];
                update_option(self::OPTION_KEY, $alerts);
            }
            wp_safe_redirect(admin_url('admin.php?page=product-purchase-alerts&ppa_msg=toggled'));
            exit;
        }

        // Save alert (add or edit)
        if (!isset($_POST['ppa_save_alert'], $_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'ppa_save_alert')) {
            return;
        }

        $alerts = self::get_alerts();

        $product_id = absint($_POST['ppa_product_id'] ?? 0);
        $emails_raw = sanitize_textarea_field($_POST['ppa_emails'] ?? '');
        $subject = sanitize_text_field($_POST['ppa_subject'] ?? '');
        $message = sanitize_textarea_field($_POST['ppa_message'] ?? '');
        $trigger = sanitize_text_field($_POST['ppa_trigger'] ?? 'completed');

        // Parse emails: one per line or comma-separated
        $emails = array_filter(array_map('trim', preg_split('/[\n,]+/', $emails_raw)));
        $emails = array_filter($emails, 'is_email');
        $emails = array_values(array_unique($emails));

        if (!$product_id || empty($emails)) {
            wp_safe_redirect(admin_url('admin.php?page=product-purchase-alerts&ppa_msg=error'));
            exit;
        }

        $alert = [
            'product_id' => $product_id,
            'emails'     => $emails,
            'subject'    => $subject ?: 'Product Purchased: {product_name}',
            'message'    => $message ?: "A customer has purchased {product_name}.\n\nOrder #{order_id}\nCustomer: {customer_name} ({customer_email})\nQuantity: {quantity}",
            'trigger'    => $trigger,
            'enabled'    => true,
        ];

        $edit_index = isset($_POST['ppa_edit_index']) && $_POST['ppa_edit_index'] !== '' ? absint($_POST['ppa_edit_index']) : null;

        if ($edit_index !== null && isset($alerts[$edit_index])) {
            $alert['enabled'] = $alerts[$edit_index]['enabled'];
            $alerts[$edit_index] = $alert;
        } else {
            $alerts[] = $alert;
        }

        update_option(self::OPTION_KEY, $alerts);
        wp_safe_redirect(admin_url('admin.php?page=product-purchase-alerts&ppa_msg=saved'));
        exit;
    }

    /**
     * When an order is completed or paid, check for matching alerts and send emails.
     */
    public function check_order_for_alerts(int $order_id): void {
        $order = wc_get_order($order_id);
        if (!$order) {
            return;
        }

        $current_hook = current_action();
        $alerts = self::get_alerts();

        foreach ($order->get_items() as $item) {
            $product_id = $item->get_product_id();

            foreach ($alerts as $alert) {
                if (!$alert['enabled']) {
                    continue;
                }

                if ((int) $alert['product_id'] !== $product_id) {
                    continue;
                }

                // Check trigger matches current hook
                $trigger = $alert['trigger'] ?? 'completed';
                if ($trigger === 'completed' && $current_hook !== 'woocommerce_order_status_completed') {
                    continue;
                }
                if ($trigger === 'payment' && $current_hook !== 'woocommerce_payment_complete') {
                    continue;
                }

                $this->send_alert($alert, $order, $item);
            }
        }
    }

    /**
     * Send the alert email.
     */
    private function send_alert(array $alert, \WC_Order $order, \WC_Order_Item_Product $item): void {
        $product = $item->get_product();
        $product_name = $product ? $product->get_name() : $item->get_name();
        $customer_name = trim($order->get_billing_first_name() . ' ' . $order->get_billing_last_name());

        $placeholders = [
            '{product_name}'    => $product_name,
            '{order_id}'        => $order->get_id(),
            '{order_total}'     => $order->get_formatted_order_total(),
            '{customer_name}'   => $customer_name,
            '{customer_email}'  => $order->get_billing_email(),
            '{quantity}'        => $item->get_quantity(),
            '{order_date}'      => $order->get_date_created() ? $order->get_date_created()->date_i18n('F j, Y g:i a') : '',
        ];

        $subject = strtr($alert['subject'], $placeholders);
        $message = strtr($alert['message'], $placeholders);
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        foreach ($alert['emails'] as $email) {
            wp_mail($email, $subject, $message, $headers);
        }
    }

    /**
     * Render the admin settings page.
     */
    public function render_settings_page(): void {
        $alerts = self::get_alerts();
        $editing = isset($_GET['ppa_edit']) ? absint($_GET['ppa_edit']) : null;
        $edit_alert = ($editing !== null && isset($alerts[$editing])) ? $alerts[$editing] : null;
        $show_form = isset($_GET['ppa_add']) || $edit_alert;

        // Flash messages
        $messages = [
            'saved'   => 'Alert saved.',
            'deleted' => 'Alert deleted.',
            'toggled' => 'Alert updated.',
            'error'   => 'Please select a product and enter at least one valid email address.',
        ];
        $msg_key = $_GET['ppa_msg'] ?? '';
        ?>
        <div class="wrap">
            <h1>Purchase Alerts</h1>
            <p>Send email notifications when specific products are purchased.</p>

            <?php if (isset($messages[$msg_key])): ?>
                <div class="notice notice-<?php echo $msg_key === 'error' ? 'error' : 'success'; ?> is-dismissible">
                    <p><?php echo esc_html($messages[$msg_key]); ?></p>
                </div>
            <?php endif; ?>

            <?php if (!$show_form): ?>
                <p><a href="<?php echo esc_url(admin_url('admin.php?page=product-purchase-alerts&ppa_add=1')); ?>" class="button button-primary">Add New Alert</a></p>
            <?php endif; ?>

            <?php if ($show_form): ?>
                <?php $this->render_form($edit_alert, $editing); ?>
            <?php endif; ?>

            <?php if (!empty($alerts)): ?>
                <table class="ppa-alerts-table widefat striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Recipients</th>
                            <th>Trigger</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alerts as $i => $alert): ?>
                            <?php $product = wc_get_product($alert['product_id']); ?>
                            <tr>
                                <td>
                                    <?php if ($product): ?>
                                        <strong><?php echo esc_html($product->get_name()); ?></strong>
                                        <br><small>ID: <?php echo esc_html($alert['product_id']); ?></small>
                                    <?php else: ?>
                                        <em>Product #<?php echo esc_html($alert['product_id']); ?> (not found)</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php foreach ($alert['emails'] as $email): ?>
                                        <span class="ppa-badge"><?php echo esc_html($email); ?></span>
                                    <?php endforeach; ?>
                                </td>
                                <td><?php echo $alert['trigger'] === 'payment' ? 'Payment received' : 'Order completed'; ?></td>
                                <td>
                                    <?php if ($alert['enabled']): ?>
                                        <span class="ppa-status-enabled">Active</span>
                                    <?php else: ?>
                                        <span class="ppa-status-disabled">Disabled</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=product-purchase-alerts&ppa_edit=' . $i)); ?>">Edit</a>
                                    &nbsp;|&nbsp;
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=product-purchase-alerts&ppa_toggle=' . $i), 'ppa_toggle')); ?>">
                                        <?php echo $alert['enabled'] ? 'Disable' : 'Enable'; ?>
                                    </a>
                                    &nbsp;|&nbsp;
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=product-purchase-alerts&ppa_delete=' . $i), 'ppa_delete')); ?>"
                                       onclick="return confirm('Delete this alert?');"
                                       style="color: #b32d2e;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php elseif (!$show_form): ?>
                <p><em>No alerts configured yet.</em></p>
            <?php endif; ?>

            <hr>
            <h3>Template Placeholders</h3>
            <p>Use these in the subject and message fields:</p>
            <code>{product_name}</code> <code>{order_id}</code> <code>{order_total}</code>
            <code>{customer_name}</code> <code>{customer_email}</code> <code>{quantity}</code> <code>{order_date}</code>
        </div>
        <?php
    }

    /**
     * Render the add/edit form.
     */
    private function render_form(?array $alert, ?int $index): void {
        $product_id = $alert['product_id'] ?? '';
        $emails     = $alert ? implode("\n", $alert['emails']) : '';
        $subject    = $alert['subject'] ?? 'Product Purchased: {product_name}';
        $message    = $alert['message'] ?? "A customer has purchased {product_name}.\n\nOrder #{order_id}\nCustomer: {customer_name} ({customer_email})\nQuantity: {quantity}";
        $trigger    = $alert['trigger'] ?? 'completed';

        // Preload selected product name for Select2
        $selected_product_name = '';
        if ($product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                $selected_product_name = $product->get_name() . ' (#' . $product_id . ')';
            }
        }
        ?>
        <div class="ppa-alert-form">
            <h2><?php echo $alert ? 'Edit Alert' : 'New Alert'; ?></h2>
            <form method="post">
                <?php wp_nonce_field('ppa_save_alert'); ?>
                <input type="hidden" name="ppa_save_alert" value="1">
                <?php if ($index !== null): ?>
                    <input type="hidden" name="ppa_edit_index" value="<?php echo esc_attr($index); ?>">
                <?php endif; ?>

                <div class="field">
                    <label for="ppa_product_id">Product</label>
                    <select name="ppa_product_id" id="ppa_product_id" class="ppa-product-search" style="width:100%">
                        <?php if ($product_id): ?>
                            <option value="<?php echo esc_attr($product_id); ?>" selected><?php echo esc_html($selected_product_name); ?></option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="ppa_emails">Email Recipients <small>(one per line or comma-separated)</small></label>
                    <textarea name="ppa_emails" id="ppa_emails" rows="3" placeholder="person@example.com&#10;another@example.com"><?php echo esc_textarea($emails); ?></textarea>
                </div>

                <div class="field">
                    <label for="ppa_trigger">Send When</label>
                    <select name="ppa_trigger" id="ppa_trigger">
                        <option value="completed" <?php selected($trigger, 'completed'); ?>>Order completed</option>
                        <option value="payment" <?php selected($trigger, 'payment'); ?>>Payment received</option>
                        <option value="both" <?php selected($trigger, 'both'); ?>>Both (payment received & order completed)</option>
                    </select>
                </div>

                <div class="field">
                    <label for="ppa_subject">Email Subject</label>
                    <input type="text" name="ppa_subject" id="ppa_subject" value="<?php echo esc_attr($subject); ?>">
                </div>

                <div class="field">
                    <label for="ppa_message">Email Message</label>
                    <textarea name="ppa_message" id="ppa_message" rows="6"><?php echo esc_textarea($message); ?></textarea>
                </div>

                <p>
                    <button type="submit" class="button button-primary"><?php echo $alert ? 'Update Alert' : 'Add Alert'; ?></button>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=product-purchase-alerts')); ?>" class="button">Cancel</a>
                </p>
            </form>
        </div>

        <script>
        jQuery(function($) {
            $('.ppa-product-search').select2({
                ajax: {
                    url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                    dataType: 'json',
                    delay: 300,
                    data: function(params) {
                        return { action: 'ppa_search_products', q: params.term };
                    },
                    processResults: function(data) {
                        return { results: data };
                    }
                },
                minimumInputLength: 2,
                placeholder: 'Search for a product...',
                allowClear: true
            });
        });
        </script>
        <?php
    }
}

/**
 * AJAX handler for product search (used by Select2).
 */
add_action('wp_ajax_ppa_search_products', function () {
    if (!current_user_can('manage_woocommerce')) {
        wp_send_json([]);
    }

    $term = sanitize_text_field($_GET['q'] ?? '');
    if (strlen($term) < 2) {
        wp_send_json([]);
    }

    $products = wc_get_products([
        'status' => 'publish',
        's'      => $term,
        'limit'  => 20,
    ]);

    $results = [];
    foreach ($products as $product) {
        $results[] = [
            'id'   => $product->get_id(),
            'text' => $product->get_name() . ' (#' . $product->get_id() . ')',
        ];
    }

    wp_send_json($results);
});

new Product_Purchase_Alerts();
