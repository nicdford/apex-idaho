<?php
/*
Plugin Name: Pitwall
Description: AI-powered event logistics for WordPress. Read-only REST API exposing Event Tickets attendees, tickets, and capacity for the Pitwall bot.
Version: 0.2.0
Author: Nic D. Ford
Author URI: https://nicdford.com
*/

if (!defined('ABSPATH')) {
    exit;
}

const PITWALL_KEY_OPTION = 'pitwall_api_key';
const PITWALL_NS         = 'pitwall/v1';

add_action('plugins_loaded', function () {
    $current = get_option(PITWALL_KEY_OPTION, '');
    $legacy  = get_option('attendee_api_key', '');
    if ($current === '' && $legacy !== '') {
        update_option(PITWALL_KEY_OPTION, $legacy);
        delete_option('attendee_api_key');
    }
});

add_action('admin_menu', function () {
    add_options_page(
        'Pitwall',
        'Pitwall',
        'manage_options',
        'pitwall',
        'pitwall_render_settings'
    );
});

add_action('admin_init', function () {
    register_setting('pitwall', PITWALL_KEY_OPTION, [
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    ]);
});

function pitwall_render_settings()
{
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }
    $key = get_option(PITWALL_KEY_OPTION, '');
    ?>
    <div class="wrap">
        <h1>Pitwall</h1>
        <p>Provide this API key to the Pitwall bot as <code>WP_API_KEY</code>. Requests must include header <code>X-Api-Key</code>.</p>
        <form method="post" action="options.php">
            <?php settings_fields('pitwall'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="pitwall_api_key">API Key</label></th>
                    <td>
                        <input type="text" id="pitwall_api_key" name="<?php echo esc_attr(PITWALL_KEY_OPTION); ?>"
                            value="<?php echo esc_attr($key); ?>" class="regular-text" style="font-family:monospace;" />
                        <p class="description">Generate a long random string (e.g. <code>openssl rand -hex 32</code>).</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function pitwall_check_auth(WP_REST_Request $request)
{
    $stored = (string) get_option(PITWALL_KEY_OPTION, '');
    if ($stored === '') {
        return new WP_Error('pitwall_no_key', 'API key not configured', ['status' => 503]);
    }
    $provided = (string) $request->get_header('x_api_key');
    if (!hash_equals($stored, $provided)) {
        return new WP_Error('pitwall_forbidden', 'Invalid API key', ['status' => 401]);
    }
    return true;
}

add_action('rest_api_init', function () {
    register_rest_route(PITWALL_NS, '/events', [
        'methods'             => 'GET',
        'permission_callback' => 'pitwall_check_auth',
        'callback'            => 'pitwall_route_events',
    ]);

    register_rest_route(PITWALL_NS, '/events/(?P<event_id>\d+)/tickets', [
        'methods'             => 'GET',
        'permission_callback' => 'pitwall_check_auth',
        'callback'            => 'pitwall_route_event_tickets',
        'args'                => [
            'event_id' => ['validate_callback' => fn($v) => is_numeric($v)],
        ],
    ]);

    register_rest_route(PITWALL_NS, '/events/(?P<event_id>\d+)/attendees', [
        'methods'             => 'GET',
        'permission_callback' => 'pitwall_check_auth',
        'callback'            => 'pitwall_route_event_attendees',
        'args'                => [
            'event_id'     => ['validate_callback' => fn($v) => is_numeric($v)],
            'ticket_id'    => ['required' => false],
            'status'       => ['required' => false],
            'include_meta' => ['required' => false],
        ],
    ]);

    register_rest_route(PITWALL_NS, '/attendees/search', [
        'methods'             => 'GET',
        'permission_callback' => 'pitwall_check_auth',
        'callback'            => 'pitwall_route_attendee_search',
        'args'                => [
            'q'            => ['required' => true],
            'event_id'     => ['required' => false],
            'include_meta' => ['required' => false],
        ],
    ]);
});

function pitwall_get_events_with_tickets()
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

function pitwall_route_events()
{
    if (!class_exists('Tribe__Tickets__Tickets')) {
        return new WP_Error('pitwall_no_et', 'Event Tickets not active', ['status' => 503]);
    }
    $events = pitwall_get_events_with_tickets();
    $out = [];
    foreach ($events as $e) {
        $start = get_post_meta($e->ID, '_EventStartDate', true);
        $end   = get_post_meta($e->ID, '_EventEndDate', true);
        $out[] = [
            'id'    => (int) $e->ID,
            'title' => $e->post_title,
            'start' => $start ?: null,
            'end'   => $end ?: null,
        ];
    }
    return rest_ensure_response($out);
}

function pitwall_ticket_summary($ticket)
{
    $capacity  = method_exists($ticket, 'capacity') ? $ticket->capacity() : null;
    $available = method_exists($ticket, 'available') ? $ticket->available() : null;
    $stock     = property_exists($ticket, 'stock') ? $ticket->stock : null;
    $sold      = property_exists($ticket, 'qty_sold') ? (int) $ticket->qty_sold : null;
    $pending   = property_exists($ticket, 'qty_pending') ? (int) $ticket->qty_pending : null;

    if (is_string($capacity) && $capacity === '') {
        $capacity = null;
    }
    if (is_string($available) && $available === '') {
        $available = null;
    }

    return [
        'id'        => (int) $ticket->ID,
        'name'      => $ticket->name,
        'price'     => isset($ticket->price) ? (string) $ticket->price : null,
        'capacity'  => is_numeric($capacity) ? (int) $capacity : ($capacity === -1 ? 'unlimited' : $capacity),
        'sold'      => $sold,
        'pending'   => $pending,
        'available' => is_numeric($available) ? (int) $available : ($available === -1 ? 'unlimited' : $available),
        'stock'     => is_numeric($stock) ? (int) $stock : $stock,
    ];
}

function pitwall_route_event_tickets(WP_REST_Request $req)
{
    if (!class_exists('Tribe__Tickets__Tickets')) {
        return new WP_Error('pitwall_no_et', 'Event Tickets not active', ['status' => 503]);
    }
    $event_id = (int) $req['event_id'];
    $tickets = Tribe__Tickets__Tickets::get_all_event_tickets($event_id);
    $out = [];
    foreach ($tickets as $t) {
        $out[] = pitwall_ticket_summary($t);
    }
    return rest_ensure_response([
        'event_id'    => $event_id,
        'event_title' => get_the_title($event_id),
        'tickets'     => $out,
    ]);
}

function pitwall_format_attendee($a, $include_meta)
{
    $row = [
        'attendee_id'     => isset($a['attendee_id']) ? (int) $a['attendee_id'] : null,
        'order_id'        => isset($a['order_id']) ? (int) $a['order_id'] : null,
        'ticket_id'       => isset($a['product_id']) ? (int) $a['product_id'] : null,
        'ticket'          => $a['ticket'] ?? null,
        'holder_name'     => $a['holder_name'] ?? null,
        'holder_email'    => $a['holder_email'] ?? null,
        'purchaser_name'  => $a['purchaser_name'] ?? null,
        'purchaser_email' => $a['purchaser_email'] ?? null,
        'order_status'    => $a['order_status'] ?? null,
        'security_code'   => $a['security_code'] ?? null,
    ];
    if ($include_meta && !empty($a['attendee_meta']) && is_array($a['attendee_meta'])) {
        $meta = [];
        foreach ($a['attendee_meta'] as $slug => $m) {
            if (!is_array($m)) {
                continue;
            }
            $value = $m['value'] ?? '';
            if (is_array($value)) {
                $value = implode(', ', array_filter(array_map('strval', $value)));
            }
            $meta[$slug] = [
                'label' => $m['label'] ?? $slug,
                'value' => (string) $value,
            ];
        }
        $row['meta'] = $meta;
    }
    return $row;
}

function pitwall_filter_attendees($attendees, $ticket_id, $status)
{
    if ($ticket_id) {
        $ticket_id = (int) $ticket_id;
        $attendees = array_filter($attendees, fn($a) => (int) ($a['product_id'] ?? 0) === $ticket_id);
    }
    if ($status) {
        $statuses = array_map('trim', explode(',', $status));
        $attendees = array_filter($attendees, fn($a) => in_array($a['order_status'] ?? '', $statuses, true));
    }
    return array_values($attendees);
}

function pitwall_route_event_attendees(WP_REST_Request $req)
{
    if (!class_exists('Tribe__Tickets__Tickets')) {
        return new WP_Error('pitwall_no_et', 'Event Tickets not active', ['status' => 503]);
    }
    $event_id     = (int) $req['event_id'];
    $ticket_id    = $req->get_param('ticket_id');
    $status       = $req->get_param('status');
    $include_meta = filter_var($req->get_param('include_meta'), FILTER_VALIDATE_BOOLEAN);

    $attendees = tribe_tickets_get_attendees($event_id);
    $attendees = pitwall_filter_attendees($attendees, $ticket_id, $status);

    $out = [];
    foreach ($attendees as $a) {
        $out[] = pitwall_format_attendee($a, $include_meta);
    }

    return rest_ensure_response([
        'event_id'    => $event_id,
        'event_title' => get_the_title($event_id),
        'count'       => count($out),
        'attendees'   => $out,
    ]);
}

function pitwall_route_attendee_search(WP_REST_Request $req)
{
    if (!class_exists('Tribe__Tickets__Tickets')) {
        return new WP_Error('pitwall_no_et', 'Event Tickets not active', ['status' => 503]);
    }
    $q            = strtolower(trim((string) $req->get_param('q')));
    $event_id     = (int) $req->get_param('event_id');
    $include_meta = filter_var($req->get_param('include_meta'), FILTER_VALIDATE_BOOLEAN);
    if ($q === '') {
        return new WP_Error('pitwall_bad_query', 'Missing q parameter', ['status' => 400]);
    }

    $events = $event_id ? [(object) ['ID' => $event_id, 'post_title' => get_the_title($event_id)]] : pitwall_get_events_with_tickets();

    $matches = [];
    foreach ($events as $e) {
        $attendees = tribe_tickets_get_attendees($e->ID);
        foreach ($attendees as $a) {
            $haystack = strtolower(($a['holder_name'] ?? '') . ' ' . ($a['holder_email'] ?? '') . ' ' . ($a['purchaser_name'] ?? '') . ' ' . ($a['purchaser_email'] ?? ''));
            if (strpos($haystack, $q) !== false) {
                $row = pitwall_format_attendee($a, $include_meta);
                $row['event_id']    = (int) $e->ID;
                $row['event_title'] = $e->post_title;
                $matches[] = $row;
            }
        }
    }

    return rest_ensure_response([
        'query'   => $q,
        'count'   => count($matches),
        'matches' => $matches,
    ]);
}
