<?php
/*
Plugin Name: Attendee Export
Description: Export Event Tickets attendees to CSV with selectable fields and ticket filtering. Parses attendee meta into usable columns.
Version: 1.0.0
Author: Nic D. Ford
Author URI: https://nicdford.com
*/

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', function () {
    add_submenu_page(
        'tec-tickets',
        'Attendee Export',
        'Attendee Export',
        'manage_options',
        'attendee-export',
        'attendee_export_render_page'
    );
});

function attendee_export_get_events_with_tickets()
{
    global $wpdb;
    $rows = $wpdb->get_results("
        SELECT DISTINCT p.ID, p.post_title
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON pm.meta_value = p.ID
        WHERE pm.meta_key IN ('_tribe_wooticket_for_event', '_tribe_tpp_for_event', '_tec_tickets_commerce_event')
          AND p.post_status = 'publish'
        ORDER BY p.post_title ASC
    ");
    return $rows ?: [];
}

function attendee_export_get_tickets_for_event($event_id)
{
    if (!function_exists('tribe_tickets_get_ticket_stock_message') && !class_exists('Tribe__Tickets__Tickets')) {
        return [];
    }
    $tickets = Tribe__Tickets__Tickets::get_all_event_tickets($event_id);
    $out = [];
    foreach ($tickets as $t) {
        $out[$t->ID] = $t->name;
    }
    return $out;
}

function attendee_export_collect_meta_keys($attendees)
{
    $keys = [];
    foreach ($attendees as $a) {
        if (!empty($a['attendee_meta']) && is_array($a['attendee_meta'])) {
            foreach ($a['attendee_meta'] as $slug => $meta) {
                $label = isset($meta['label']) ? $meta['label'] : $slug;
                $keys[$slug] = $label;
            }
        }
    }
    return $keys;
}

function attendee_export_meta_value($meta)
{
    if (!is_array($meta)) {
        return '';
    }
    $value = isset($meta['value']) ? $meta['value'] : '';
    if (is_array($value)) {
        return implode(', ', array_filter(array_map('strval', $value)));
    }
    return (string) $value;
}

function attendee_export_render_page()
{
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    if (!class_exists('Tribe__Tickets__Tickets')) {
        echo '<div class="wrap"><h1>Attendee Export</h1><p>Event Tickets is not active.</p></div>';
        return;
    }

    $events = attendee_export_get_events_with_tickets();
    $event_id = isset($_GET['event_id']) ? absint($_GET['event_id']) : 0;
    $tickets = $event_id ? attendee_export_get_tickets_for_event($event_id) : [];
    $attendees = $event_id ? tribe_tickets_get_attendees($event_id) : [];
    $meta_keys = attendee_export_collect_meta_keys($attendees);

    $base_fields = [
        'order_id'      => 'Order ID',
        'ticket'        => 'Ticket',
        'holder_name'   => 'Attendee Name',
        'holder_email'  => 'Attendee Email',
        'purchaser_name'  => 'Purchaser Name',
        'purchaser_email' => 'Purchaser Email',
        'order_status'  => 'Order Status',
        'security_code' => 'Security Code',
    ];

    ?>
    <div class="wrap">
        <h1>Attendee Export</h1>

        <form method="get">
            <input type="hidden" name="page" value="attendee-export" />
            <table class="form-table">
                <tr>
                    <th><label for="event_id">Event</label></th>
                    <td>
                        <select name="event_id" id="event_id" onchange="this.form.submit()">
                            <option value="">— Select event —</option>
                            <?php foreach ($events as $e) : ?>
                                <option value="<?php echo esc_attr($e->ID); ?>" <?php selected($event_id, $e->ID); ?>>
                                    <?php echo esc_html($e->post_title); ?> (#<?php echo (int) $e->ID; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
        </form>

        <?php if ($event_id && !empty($attendees)) : ?>
            <form method="post">
                <?php wp_nonce_field('attendee_export_run', 'attendee_export_nonce'); ?>
                <input type="hidden" name="event_id" value="<?php echo esc_attr($event_id); ?>" />

                <h2>Filter by Ticket</h2>
                <p>Leave all unchecked to include every ticket.</p>
                <?php foreach ($tickets as $tid => $tname) : ?>
                    <label style="display:block;margin:4px 0;">
                        <input type="checkbox" name="ticket_ids[]" value="<?php echo esc_attr($tid); ?>" />
                        <?php echo esc_html($tname); ?>
                    </label>
                <?php endforeach; ?>

                <h2>Standard Fields</h2>
                <?php foreach ($base_fields as $key => $label) : ?>
                    <label style="display:inline-block;min-width:240px;margin:4px 0;">
                        <input type="checkbox" name="fields[]" value="<?php echo esc_attr($key); ?>"
                            <?php checked(in_array($key, ['holder_name', 'holder_email', 'ticket', 'order_id'])); ?> />
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>

                <h2>Attendee Meta Fields</h2>
                <?php if (empty($meta_keys)) : ?>
                    <p><em>No attendee meta found for this event.</em></p>
                <?php else : ?>
                    <?php foreach ($meta_keys as $slug => $label) : ?>
                        <label style="display:inline-block;min-width:240px;margin:4px 0;">
                            <input type="checkbox" name="meta_fields[]" value="<?php echo esc_attr($slug); ?>" checked />
                            <?php echo esc_html($label); ?>
                            <code style="font-size:11px;color:#888;"><?php echo esc_html($slug); ?></code>
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>

                <p style="margin-top:20px;">
                    <button type="submit" name="attendee_export_action" value="download" class="button button-primary">
                        Download CSV
                    </button>
                    <span style="margin-left:12px;color:#666;">
                        <?php echo count($attendees); ?> attendee(s) total for this event
                    </span>
                </p>
            </form>
        <?php elseif ($event_id) : ?>
            <p>No attendees found for this event.</p>
        <?php endif; ?>
    </div>
    <?php
}

add_action('admin_init', function () {
    if (empty($_POST['attendee_export_action']) || $_POST['attendee_export_action'] !== 'download') {
        return;
    }
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    check_admin_referer('attendee_export_run', 'attendee_export_nonce');

    $event_id = isset($_POST['event_id']) ? absint($_POST['event_id']) : 0;
    if (!$event_id) {
        wp_die('Missing event');
    }

    $ticket_ids   = isset($_POST['ticket_ids']) ? array_map('absint', (array) $_POST['ticket_ids']) : [];
    $fields       = isset($_POST['fields']) ? array_map('sanitize_text_field', (array) $_POST['fields']) : [];
    $meta_fields  = isset($_POST['meta_fields']) ? array_map('sanitize_text_field', (array) $_POST['meta_fields']) : [];

    $attendees = tribe_tickets_get_attendees($event_id);

    if (!empty($ticket_ids)) {
        $attendees = array_filter($attendees, function ($a) use ($ticket_ids) {
            $tid = isset($a['product_id']) ? (int) $a['product_id'] : 0;
            return in_array($tid, $ticket_ids, true);
        });
    }

    $base_labels = [
        'order_id'        => 'Order ID',
        'ticket'          => 'Ticket',
        'holder_name'     => 'Attendee Name',
        'holder_email'    => 'Attendee Email',
        'purchaser_name'  => 'Purchaser Name',
        'purchaser_email' => 'Purchaser Email',
        'order_status'    => 'Order Status',
        'security_code'   => 'Security Code',
    ];

    $meta_labels = attendee_export_collect_meta_keys($attendees);

    $headers = [];
    foreach ($fields as $f) {
        $headers[] = isset($base_labels[$f]) ? $base_labels[$f] : $f;
    }
    foreach ($meta_fields as $m) {
        $headers[] = isset($meta_labels[$m]) ? $meta_labels[$m] : $m;
    }

    $filename = 'attendees-event-' . $event_id . '-' . date('Ymd-His') . '.csv';
    nocache_headers();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $out = fopen('php://output', 'w');
    fputcsv($out, $headers);

    foreach ($attendees as $a) {
        $row = [];
        foreach ($fields as $f) {
            $row[] = isset($a[$f]) ? (is_scalar($a[$f]) ? $a[$f] : wp_json_encode($a[$f])) : '';
        }
        foreach ($meta_fields as $m) {
            $meta = isset($a['attendee_meta'][$m]) ? $a['attendee_meta'][$m] : null;
            $row[] = attendee_export_meta_value($meta);
        }
        fputcsv($out, $row);
    }

    fclose($out);
    exit;
});
