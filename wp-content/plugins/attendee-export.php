<?php
/*
Plugin Name: Attendee Export
Description: Export Event Tickets attendees to CSV with selectable fields, ticket and status filtering, sorting, and saved export presets.
Version: 1.1.0
Author: Nic D. Ford
Author URI: https://nicdford.com
*/

if (!defined('ABSPATH')) {
    exit;
}

const ATTENDEE_EXPORT_OPTION = 'attendee_export_presets';

add_action('admin_menu', function () {
    add_submenu_page(
        null,
        'Attendee Export',
        'Attendee Export',
        'manage_options',
        'attendee-export',
        'attendee_export_render_page'
    );
}, 9);

add_action('admin_menu', function () {
    global $submenu;
    $parents = ['tec-tickets', 'tribe-common', 'edit.php?post_type=tribe_events'];
    foreach ($parents as $parent) {
        if (!empty($submenu[$parent])) {
            add_submenu_page(
                $parent,
                'Attendee Export',
                'Attendee Export',
                'manage_options',
                'attendee-export',
                'attendee_export_render_page'
            );
            return;
        }
    }
}, 999);

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
    if (!class_exists('Tribe__Tickets__Tickets')) {
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

function attendee_export_get_presets()
{
    $p = get_option(ATTENDEE_EXPORT_OPTION, []);
    return is_array($p) ? $p : [];
}

function attendee_export_save_preset($name, $config)
{
    $presets = attendee_export_get_presets();
    $presets[$name] = $config;
    update_option(ATTENDEE_EXPORT_OPTION, $presets);
}

function attendee_export_delete_preset($name)
{
    $presets = attendee_export_get_presets();
    unset($presets[$name]);
    update_option(ATTENDEE_EXPORT_OPTION, $presets);
}

function attendee_export_config_from_post()
{
    return [
        'event_id'    => isset($_POST['event_id']) ? absint($_POST['event_id']) : 0,
        'ticket_ids'  => isset($_POST['ticket_ids']) ? array_map('absint', (array) $_POST['ticket_ids']) : [],
        'statuses'    => isset($_POST['statuses']) ? array_map('sanitize_text_field', (array) $_POST['statuses']) : [],
        'fields'      => isset($_POST['fields']) ? array_map('sanitize_text_field', (array) $_POST['fields']) : [],
        'meta_fields' => isset($_POST['meta_fields']) ? array_map('sanitize_text_field', (array) $_POST['meta_fields']) : [],
        'sort_by'     => isset($_POST['sort_by']) ? sanitize_text_field($_POST['sort_by']) : '',
        'sort_meta'   => isset($_POST['sort_meta']) ? sanitize_text_field($_POST['sort_meta']) : '',
        'sort_dir'    => (isset($_POST['sort_dir']) && $_POST['sort_dir'] === 'desc') ? 'desc' : 'asc',
    ];
}

function attendee_export_run($config)
{
    $event_id = (int) ($config['event_id'] ?? 0);
    if (!$event_id) {
        wp_die('Missing event');
    }

    $attendees = tribe_tickets_get_attendees($event_id);

    if (!empty($config['ticket_ids'])) {
        $ticket_ids = array_map('intval', $config['ticket_ids']);
        $attendees = array_filter($attendees, function ($a) use ($ticket_ids) {
            $tid = isset($a['product_id']) ? (int) $a['product_id'] : 0;
            return in_array($tid, $ticket_ids, true);
        });
    }

    if (!empty($config['statuses'])) {
        $statuses = $config['statuses'];
        $attendees = array_filter($attendees, function ($a) use ($statuses) {
            $s = isset($a['order_status']) && $a['order_status'] !== '' ? $a['order_status'] : '(none)';
            return in_array($s, $statuses, true);
        });
    }

    $sort_by   = $config['sort_by'] ?? '';
    $sort_meta = $config['sort_meta'] ?? '';
    $sort_dir  = ($config['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

    if ($sort_meta || $sort_by) {
        $attendees = array_values($attendees);
        usort($attendees, function ($a, $b) use ($sort_by, $sort_meta, $sort_dir) {
            if ($sort_meta) {
                $av = attendee_export_meta_value($a['attendee_meta'][$sort_meta] ?? null);
                $bv = attendee_export_meta_value($b['attendee_meta'][$sort_meta] ?? null);
            } else {
                $av = isset($a[$sort_by]) && is_scalar($a[$sort_by]) ? (string) $a[$sort_by] : '';
                $bv = isset($b[$sort_by]) && is_scalar($b[$sort_by]) ? (string) $b[$sort_by] : '';
            }
            $cmp = strnatcasecmp($av, $bv);
            return $sort_dir === 'desc' ? -$cmp : $cmp;
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
    $fields      = $config['fields'] ?? [];
    $meta_fields = $config['meta_fields'] ?? [];

    $headers = [];
    foreach ($fields as $f) {
        $headers[] = $base_labels[$f] ?? $f;
    }
    foreach ($meta_fields as $m) {
        $headers[] = $meta_labels[$m] ?? $m;
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
}

add_action('admin_init', function () {
    if (empty($_POST['attendee_export_action'])) {
        return;
    }
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    $action = sanitize_text_field($_POST['attendee_export_action']);

    if ($action === 'download') {
        check_admin_referer('attendee_export_run', 'attendee_export_nonce');
        attendee_export_run(attendee_export_config_from_post());
    }

    if ($action === 'save_preset') {
        check_admin_referer('attendee_export_run', 'attendee_export_nonce');
        $name = isset($_POST['preset_name']) ? sanitize_text_field($_POST['preset_name']) : '';
        if ($name === '') {
            wp_die('Preset name required');
        }
        attendee_export_save_preset($name, attendee_export_config_from_post());
        wp_safe_redirect(add_query_arg(['page' => 'attendee-export', 'event_id' => absint($_POST['event_id']), 'saved' => rawurlencode($name)], admin_url('admin.php')));
        exit;
    }
});

add_action('admin_init', function () {
    if (!isset($_GET['page']) || $_GET['page'] !== 'attendee-export') {
        return;
    }
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_GET['run_preset'])) {
        check_admin_referer('attendee_export_preset_' . $_GET['run_preset']);
        $name = sanitize_text_field(rawurldecode($_GET['run_preset']));
        $presets = attendee_export_get_presets();
        if (!isset($presets[$name])) {
            wp_die('Preset not found');
        }
        attendee_export_run($presets[$name]);
    }

    if (isset($_GET['delete_preset'])) {
        check_admin_referer('attendee_export_preset_' . $_GET['delete_preset']);
        $name = sanitize_text_field(rawurldecode($_GET['delete_preset']));
        attendee_export_delete_preset($name);
        wp_safe_redirect(add_query_arg(['page' => 'attendee-export', 'deleted' => rawurlencode($name)], admin_url('admin.php')));
        exit;
    }
});

function attendee_export_render_page()
{
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    if (!class_exists('Tribe__Tickets__Tickets')) {
        echo '<div class="wrap"><h1>Attendee Export</h1><p>Event Tickets is not active.</p></div>';
        return;
    }

    $events    = attendee_export_get_events_with_tickets();
    $presets   = attendee_export_get_presets();
    $edit_name = isset($_GET['edit_preset']) ? sanitize_text_field(rawurldecode($_GET['edit_preset'])) : '';
    $loaded    = ($edit_name && isset($presets[$edit_name])) ? $presets[$edit_name] : null;

    $event_id = isset($_GET['event_id']) ? absint($_GET['event_id']) : ($loaded['event_id'] ?? 0);
    $tickets    = $event_id ? attendee_export_get_tickets_for_event($event_id) : [];
    $attendees  = $event_id ? tribe_tickets_get_attendees($event_id) : [];
    $meta_keys  = attendee_export_collect_meta_keys($attendees);

    $statuses_avail = [];
    foreach ($attendees as $a) {
        $s = isset($a['order_status']) && $a['order_status'] !== '' ? $a['order_status'] : '(none)';
        $statuses_avail[$s] = ($statuses_avail[$s] ?? 0) + 1;
    }
    ksort($statuses_avail);

    $base_fields = [
        'order_id'        => 'Order ID',
        'ticket'          => 'Ticket',
        'holder_name'     => 'Attendee Name',
        'holder_email'    => 'Attendee Email',
        'purchaser_name'  => 'Purchaser Name',
        'purchaser_email' => 'Purchaser Email',
        'order_status'    => 'Order Status',
        'security_code'   => 'Security Code',
    ];

    $sel_fields  = $loaded['fields']      ?? ['holder_name', 'holder_email', 'ticket', 'order_id'];
    $sel_meta    = $loaded['meta_fields'] ?? array_keys($meta_keys);
    $sel_tickets = $loaded['ticket_ids']  ?? [];
    $sel_status  = $loaded['statuses']    ?? ['completed'];
    $sort_by     = $loaded['sort_by']     ?? 'holder_name';
    $sort_meta   = $loaded['sort_meta']   ?? '';
    $sort_dir    = $loaded['sort_dir']    ?? 'asc';

    ?>
    <div class="wrap">
        <h1>Attendee Export</h1>

        <?php if (!empty($_GET['saved'])) : ?>
            <div class="notice notice-success is-dismissible"><p>Saved preset "<?php echo esc_html(rawurldecode($_GET['saved'])); ?>".</p></div>
        <?php endif; ?>
        <?php if (!empty($_GET['deleted'])) : ?>
            <div class="notice notice-success is-dismissible"><p>Deleted preset "<?php echo esc_html(rawurldecode($_GET['deleted'])); ?>".</p></div>
        <?php endif; ?>

        <h2>Saved Exports</h2>
        <?php if (empty($presets)) : ?>
            <p><em>No saved exports yet. Configure an export below and click "Save as preset".</em></p>
        <?php else : ?>
            <table class="widefat striped" style="max-width:900px;">
                <thead>
                    <tr><th>Name</th><th>Event</th><th>Filters</th><th style="width:280px;">Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($presets as $name => $cfg) :
                        $event_title = get_the_title($cfg['event_id'] ?? 0);
                        $summary = [];
                        if (!empty($cfg['ticket_ids'])) $summary[] = count($cfg['ticket_ids']) . ' ticket(s)';
                        if (!empty($cfg['statuses'])) $summary[] = implode('/', $cfg['statuses']);
                        if (!empty($cfg['fields'])) $summary[] = count($cfg['fields']) . ' fields';
                        if (!empty($cfg['meta_fields'])) $summary[] = count($cfg['meta_fields']) . ' meta';
                        $run_url    = wp_nonce_url(add_query_arg(['page' => 'attendee-export', 'run_preset' => rawurlencode($name)], admin_url('admin.php')), 'attendee_export_preset_' . rawurlencode($name));
                        $edit_url   = add_query_arg(['page' => 'attendee-export', 'edit_preset' => rawurlencode($name)], admin_url('admin.php'));
                        $delete_url = wp_nonce_url(add_query_arg(['page' => 'attendee-export', 'delete_preset' => rawurlencode($name)], admin_url('admin.php')), 'attendee_export_preset_' . rawurlencode($name));
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html($name); ?></strong></td>
                            <td><?php echo esc_html($event_title ?: '(missing event)'); ?></td>
                            <td><?php echo esc_html(implode(' · ', $summary)); ?></td>
                            <td>
                                <a href="<?php echo esc_url($run_url); ?>" class="button button-primary">Download CSV</a>
                                <a href="<?php echo esc_url($edit_url); ?>" class="button">Edit</a>
                                <a href="<?php echo esc_url($delete_url); ?>" class="button" onclick="return confirm('Delete preset \'<?php echo esc_js($name); ?>\'?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <hr style="margin:30px 0;" />

        <h2><?php echo $loaded ? 'Edit Preset: ' . esc_html($edit_name) : 'New Export'; ?></h2>

        <form method="get">
            <input type="hidden" name="page" value="attendee-export" />
            <?php if ($edit_name) : ?>
                <input type="hidden" name="edit_preset" value="<?php echo esc_attr($edit_name); ?>" />
            <?php endif; ?>
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

                <h3>Sort By</h3>
                <select name="sort_by">
                    <?php foreach (['holder_name'=>'Attendee Name','holder_email'=>'Attendee Email','purchaser_name'=>'Purchaser Name','ticket'=>'Ticket','order_status'=>'Order Status','order_id'=>'Order ID'] as $k => $l) : ?>
                        <option value="<?php echo esc_attr($k); ?>" <?php selected($sort_by, $k); ?>><?php echo esc_html($l); ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="sort_dir">
                    <option value="asc" <?php selected($sort_dir, 'asc'); ?>>Ascending</option>
                    <option value="desc" <?php selected($sort_dir, 'desc'); ?>>Descending</option>
                </select>
                <?php if (!empty($meta_keys)) : ?>
                    <p style="margin-top:6px;">
                        <em>Or sort by an attendee meta field:</em>
                        <select name="sort_meta">
                            <option value="">— none —</option>
                            <?php foreach ($meta_keys as $slug => $label) : ?>
                                <option value="<?php echo esc_attr($slug); ?>" <?php selected($sort_meta, $slug); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                <?php endif; ?>

                <h3>Filter by Order Status</h3>
                <p>Leave all unchecked to include every status.</p>
                <?php foreach ($statuses_avail as $status => $count) : ?>
                    <label style="display:inline-block;min-width:200px;margin:4px 0;">
                        <input type="checkbox" name="statuses[]" value="<?php echo esc_attr($status); ?>"
                            <?php checked(in_array($status, $sel_status, true)); ?> />
                        <?php echo esc_html($status); ?> <span style="color:#888;">(<?php echo (int) $count; ?>)</span>
                    </label>
                <?php endforeach; ?>

                <h3>Filter by Ticket</h3>
                <p>Leave all unchecked to include every ticket.</p>
                <?php foreach ($tickets as $tid => $tname) : ?>
                    <label style="display:block;margin:4px 0;">
                        <input type="checkbox" name="ticket_ids[]" value="<?php echo esc_attr($tid); ?>"
                            <?php checked(in_array((int) $tid, array_map('intval', $sel_tickets), true)); ?> />
                        <?php echo esc_html($tname); ?>
                    </label>
                <?php endforeach; ?>

                <h3>Standard Fields</h3>
                <?php foreach ($base_fields as $key => $label) : ?>
                    <label style="display:inline-block;min-width:240px;margin:4px 0;">
                        <input type="checkbox" name="fields[]" value="<?php echo esc_attr($key); ?>"
                            <?php checked(in_array($key, $sel_fields, true)); ?> />
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>

                <h3>Attendee Meta Fields</h3>
                <?php if (empty($meta_keys)) : ?>
                    <p><em>No attendee meta found for this event.</em></p>
                <?php else : ?>
                    <?php foreach ($meta_keys as $slug => $label) : ?>
                        <label style="display:inline-block;min-width:240px;margin:4px 0;">
                            <input type="checkbox" name="meta_fields[]" value="<?php echo esc_attr($slug); ?>"
                                <?php checked(in_array($slug, $sel_meta, true)); ?> />
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

                <hr />
                <h3>Save as Preset</h3>
                <p>
                    <input type="text" name="preset_name" placeholder="Preset name (e.g. Early Bird Shirts)"
                        value="<?php echo esc_attr($edit_name); ?>" style="min-width:280px;" />
                    <button type="submit" name="attendee_export_action" value="save_preset" class="button">
                        <?php echo $edit_name ? 'Update Preset' : 'Save Preset'; ?>
                    </button>
                    <?php if ($edit_name) : ?>
                        <span style="color:#666;">Saving with the same name will update; change the name to save as new.</span>
                    <?php endif; ?>
                </p>
            </form>
        <?php elseif ($event_id) : ?>
            <p>No attendees found for this event.</p>
        <?php endif; ?>
    </div>
    <?php
}
