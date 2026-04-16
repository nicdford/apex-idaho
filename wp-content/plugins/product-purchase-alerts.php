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

        wp_enqueue_script('wc-enhanced-select');
        wp_enqueue_style('woocommerce_admin_styles');

        wp_register_style('ppa-admin', false, [], '1.1.0');
        wp_enqueue_style('ppa-admin');
        wp_add_inline_style('ppa-admin', '
            /* ── Page layout ── */
            .ppa-wrap { max-width: 960px; }
            .ppa-header { display: flex; align-items: center; justify-content: space-between; margin: 0 0 20px; }
            .ppa-header h1 { margin: 0; }
            .ppa-desc { color: #646970; margin: -12px 0 24px; }

            /* ── Card ── */
            .ppa-card { background: #fff; border: 1px solid #c3c4c7; border-radius: 4px; padding: 24px 28px; margin-bottom: 24px; }
            .ppa-card h2 { margin: 0 0 20px; padding: 0 0 14px; border-bottom: 1px solid #e0e0e0; font-size: 15px; }

            /* ── Form fields ── */
            .ppa-field { margin-bottom: 20px; }
            .ppa-field:last-of-type { margin-bottom: 0; }
            .ppa-field label { display: block; font-weight: 600; margin-bottom: 6px; font-size: 13px; }
            .ppa-field .ppa-help { display: block; color: #646970; font-size: 12px; margin-top: 6px; line-height: 1.5; }
            .ppa-field input[type="text"],
            .ppa-field textarea { width: 100%; max-width: 100%; padding: 8px 10px; font-size: 13px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box; }
            .ppa-field input[type="text"]:focus,
            .ppa-field textarea:focus { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; outline: none; }
            .ppa-field textarea { min-height: 90px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
            .ppa-field select { padding: 6px 8px; font-size: 13px; border-radius: 4px; min-width: 240px; }
            .ppa-field-row { display: flex; gap: 20px; }
            .ppa-field-row .ppa-field { flex: 1; }

            /* Select2 overrides */
            .ppa-field .select2-container { width: 100% !important; }
            .ppa-field .select2-container--default .select2-selection--single { height: 38px; border: 1px solid #8c8f94; border-radius: 4px; padding: 4px 8px; }
            .ppa-field .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; padding-left: 0; color: #2c3338; }
            .ppa-field .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
            .ppa-field .select2-container--default .select2-selection--single .select2-selection__placeholder { color: #a7aaad; }
            .ppa-field .select2-container--default.select2-container--focus .select2-selection--single { border-color: #2271b1; box-shadow: 0 0 0 1px #2271b1; }

            /* ── Form actions ── */
            .ppa-actions { display: flex; align-items: center; gap: 10px; padding-top: 20px; margin-top: 24px; border-top: 1px solid #e0e0e0; }

            /* ── Placeholders reference ── */
            .ppa-placeholders { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
            .ppa-placeholders code { background: #f0f0f1; padding: 3px 8px; border-radius: 3px; font-size: 12px; cursor: pointer; transition: background 0.15s; border: 1px solid transparent; }
            .ppa-placeholders code:hover { background: #e2e4e7; border-color: #c3c4c7; }
            .ppa-copied-tip { font-size: 11px; color: #00a32a; margin-left: 4px; opacity: 0; transition: opacity 0.2s; }
            .ppa-copied-tip.show { opacity: 1; }

            /* ── Alerts table ── */
            .ppa-table { border-collapse: collapse; width: 100%; }
            .ppa-table thead th { text-align: left; padding: 12px 14px; font-size: 13px; font-weight: 600; color: #1d2327; border-bottom: 1px solid #c3c4c7; }
            .ppa-table tbody td { padding: 14px; vertical-align: middle; border-bottom: 1px solid #f0f0f1; }
            .ppa-table tbody tr:last-child td { border-bottom: none; }
            .ppa-table tbody tr:hover td { background: #f6f7f7; }
            .ppa-table .ppa-col-product { min-width: 180px; }
            .ppa-table .ppa-col-actions { white-space: nowrap; text-align: right; }
            .ppa-product-name { font-weight: 600; color: #1d2327; display: block; }
            .ppa-product-id { color: #a7aaad; font-size: 12px; }
            .ppa-product-missing { color: #b32d2e; font-style: italic; }

            /* ── Email badges ── */
            .ppa-badges { display: flex; flex-wrap: wrap; gap: 4px; }
            .ppa-badge { display: inline-flex; align-items: center; background: #f0f0f1; border-radius: 12px; padding: 3px 10px; font-size: 12px; color: #2c3338; }

            /* ── Status pill ── */
            .ppa-status { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; }
            .ppa-status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
            .ppa-status-dot.active { background: #00a32a; }
            .ppa-status-dot.disabled { background: #cc1818; }
            .ppa-status.active { color: #1d2327; }
            .ppa-status.disabled { color: #646970; }

            /* ── Trigger label ── */
            .ppa-trigger-label { font-size: 13px; color: #50575e; }

            /* ── Row actions ── */
            .ppa-row-actions { display: flex; gap: 6px; justify-content: flex-end; }
            .ppa-row-actions a { text-decoration: none; padding: 4px 10px; border-radius: 3px; font-size: 12px; transition: background 0.15s, color 0.15s; }
            .ppa-row-actions .ppa-action-edit { color: #2271b1; }
            .ppa-row-actions .ppa-action-edit:hover { background: #f0f6fc; }
            .ppa-row-actions .ppa-action-toggle { color: #50575e; }
            .ppa-row-actions .ppa-action-toggle:hover { background: #f0f0f1; }
            .ppa-row-actions .ppa-action-delete { color: #b32d2e; }
            .ppa-row-actions .ppa-action-delete:hover { background: #fcf0f1; }

            /* ── Empty state ── */
            .ppa-empty { text-align: center; padding: 48px 24px; }
            .ppa-empty .dashicons { font-size: 48px; width: 48px; height: 48px; color: #c3c4c7; margin-bottom: 12px; }
            .ppa-empty p { color: #646970; font-size: 14px; margin: 0 0 16px; }
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
        $trigger_labels = [
            'completed' => 'Order completed',
            'payment'   => 'Payment received',
            'both'      => 'Payment & completed',
        ];
        ?>
        <div class="wrap ppa-wrap">

            <?php if (isset($messages[$msg_key])): ?>
                <div class="notice notice-<?php echo $msg_key === 'error' ? 'error' : 'success'; ?> is-dismissible">
                    <p><?php echo esc_html($messages[$msg_key]); ?></p>
                </div>
            <?php endif; ?>

            <div class="ppa-header">
                <h1>Purchase Alerts</h1>
                <?php if (!$show_form): ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=product-purchase-alerts&ppa_add=1')); ?>" class="button button-primary">Add New Alert</a>
                <?php endif; ?>
            </div>

            <?php if (!$show_form && empty($alerts)): ?>
                <p class="ppa-desc">Send email notifications when specific products are purchased.</p>
            <?php endif; ?>

            <?php if ($show_form): ?>
                <?php $this->render_form($edit_alert, $editing); ?>
            <?php endif; ?>

            <?php if (!empty($alerts)): ?>
                <div class="ppa-card">
                    <table class="ppa-table">
                        <thead>
                            <tr>
                                <th class="ppa-col-product">Product</th>
                                <th>Recipients</th>
                                <th>Trigger</th>
                                <th>Status</th>
                                <th class="ppa-col-actions"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alerts as $i => $alert): ?>
                                <?php $product = wc_get_product($alert['product_id']); ?>
                                <tr>
                                    <td class="ppa-col-product">
                                        <?php if ($product): ?>
                                            <span class="ppa-product-name"><?php echo esc_html($product->get_name()); ?></span>
                                            <span class="ppa-product-id">#<?php echo esc_html($alert['product_id']); ?></span>
                                        <?php else: ?>
                                            <span class="ppa-product-missing">Product #<?php echo esc_html($alert['product_id']); ?> (not found)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="ppa-badges">
                                            <?php foreach ($alert['emails'] as $email): ?>
                                                <span class="ppa-badge"><?php echo esc_html($email); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ppa-trigger-label"><?php echo esc_html($trigger_labels[$alert['trigger']] ?? $trigger_labels['completed']); ?></span>
                                    </td>
                                    <td>
                                        <span class="ppa-status <?php echo $alert['enabled'] ? 'active' : 'disabled'; ?>">
                                            <span class="ppa-status-dot <?php echo $alert['enabled'] ? 'active' : 'disabled'; ?>"></span>
                                            <?php echo $alert['enabled'] ? 'Active' : 'Disabled'; ?>
                                        </span>
                                    </td>
                                    <td class="ppa-col-actions">
                                        <div class="ppa-row-actions">
                                            <a href="<?php echo esc_url(admin_url('admin.php?page=product-purchase-alerts&ppa_edit=' . $i)); ?>" class="ppa-action-edit">Edit</a>
                                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=product-purchase-alerts&ppa_toggle=' . $i), 'ppa_toggle')); ?>" class="ppa-action-toggle">
                                                <?php echo $alert['enabled'] ? 'Disable' : 'Enable'; ?>
                                            </a>
                                            <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=product-purchase-alerts&ppa_delete=' . $i), 'ppa_delete')); ?>"
                                               onclick="return confirm('Delete this alert?');"
                                               class="ppa-action-delete">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (!$show_form): ?>
                <div class="ppa-card">
                    <div class="ppa-empty">
                        <span class="dashicons dashicons-bell"></span>
                        <p>No alerts yet. Create one to get notified when a product is purchased.</p>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=product-purchase-alerts&ppa_add=1')); ?>" class="button button-primary">Add Your First Alert</a>
                    </div>
                </div>
            <?php endif; ?>

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
        <div class="ppa-card">
            <h2><?php echo $alert ? 'Edit Alert' : 'New Alert'; ?></h2>
            <form method="post">
                <?php wp_nonce_field('ppa_save_alert'); ?>
                <input type="hidden" name="ppa_save_alert" value="1">
                <?php if ($index !== null): ?>
                    <input type="hidden" name="ppa_edit_index" value="<?php echo esc_attr($index); ?>">
                <?php endif; ?>

                <div class="ppa-field">
                    <label for="ppa_product_id">Product</label>
                    <select class="wc-product-search" name="ppa_product_id" id="ppa_product_id"
                            data-placeholder="Search for a product&hellip;"
                            data-action="woocommerce_json_search_products_and_variations"
                            data-allow_clear="true"
                            style="width: 100%;">
                        <?php if ($product_id): ?>
                            <option value="<?php echo esc_attr($product_id); ?>" selected><?php echo esc_html($selected_product_name); ?></option>
                        <?php endif; ?>
                    </select>
                    <span class="ppa-help">Start typing to search your WooCommerce products.</span>
                </div>

                <div class="ppa-field">
                    <label for="ppa_emails">Email Recipients</label>
                    <textarea name="ppa_emails" id="ppa_emails" rows="3" placeholder="person@example.com&#10;another@example.com"><?php echo esc_textarea($emails); ?></textarea>
                    <span class="ppa-help">One email per line, or separate with commas. All listed recipients will be notified.</span>
                </div>

                <div class="ppa-field-row">
                    <div class="ppa-field">
                        <label for="ppa_trigger">Send When</label>
                        <select name="ppa_trigger" id="ppa_trigger">
                            <option value="completed" <?php selected($trigger, 'completed'); ?>>Order completed</option>
                            <option value="payment" <?php selected($trigger, 'payment'); ?>>Payment received</option>
                            <option value="both" <?php selected($trigger, 'both'); ?>>Both</option>
                        </select>
                    </div>
                    <div class="ppa-field">
                        <label for="ppa_subject">Email Subject</label>
                        <input type="text" name="ppa_subject" id="ppa_subject" value="<?php echo esc_attr($subject); ?>">
                    </div>
                </div>

                <div class="ppa-field">
                    <label for="ppa_message">Email Message</label>
                    <textarea name="ppa_message" id="ppa_message" rows="6"><?php echo esc_textarea($message); ?></textarea>
                    <span class="ppa-help">
                        Available placeholders (click to copy):
                        <span class="ppa-copied-tip" id="ppa-copied-tip">Copied!</span>
                    </span>
                    <div class="ppa-placeholders">
                        <code data-placeholder="{product_name}">{product_name}</code>
                        <code data-placeholder="{order_id}">{order_id}</code>
                        <code data-placeholder="{order_total}">{order_total}</code>
                        <code data-placeholder="{customer_name}">{customer_name}</code>
                        <code data-placeholder="{customer_email}">{customer_email}</code>
                        <code data-placeholder="{quantity}">{quantity}</code>
                        <code data-placeholder="{order_date}">{order_date}</code>
                    </div>
                </div>

                <div class="ppa-actions">
                    <button type="submit" class="button button-primary"><?php echo $alert ? 'Save Changes' : 'Create Alert'; ?></button>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=product-purchase-alerts')); ?>" class="button">Cancel</a>
                </div>
            </form>
        </div>

        <script>
        jQuery(function($) {
            $('.ppa-placeholders code').on('click', function() {
                var text = $(this).data('placeholder');
                navigator.clipboard.writeText(text).then(function() {
                    var $tip = $('#ppa-copied-tip');
                    $tip.addClass('show');
                    setTimeout(function() { $tip.removeClass('show'); }, 1200);
                });
            });
        });
        </script>
        <?php
    }
}

new Product_Purchase_Alerts();
