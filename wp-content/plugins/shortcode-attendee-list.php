<?php
/*
Plugin Name: Attendee List
Description: Display the attendees for a given event ID.
Version: 1.0.0
Author: Nic D. Ford
Author URI: https://nicdford.com
 */

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Add shortcode
add_shortcode('attendee_list', 'attendee_list_shortcode');

function attendee_list_shortcode($atts)
{
    // Extract attributes
    $atts = shortcode_atts(
        array(
            'event_id' => '',
            'ticket_name' => '',
        ),
        $atts,
        'attendee_list'
    );

    // Check if event_id is provided
    if (empty($atts['event_id'])) {
        return 'Please provide an event ID.';
    }

    // Check if The Events Calendar and Event Tickets are active
    if (!class_exists('Tribe__Events__Main') || !class_exists('Tribe__Tickets__Main')) {
        return 'The Events Calendar and Event Tickets plugins are required.';
    }

    // Get attendees
    $attendees = tribe_tickets_get_attendees($atts['event_id']);

    if (empty($attendees)) {
        return 'No attendees found for this event.';
    }

    // filter out any attendees where the ticket name does not match the ticket_name attribute
    $attendees = array_filter($attendees, function ($attendee) use ($atts) {
        return $attendee['ticket'] === $atts['ticket_name'];
    });

    // Start output buffering
    ob_start();

    // Display attendees
    echo '<table class="attendee-list" style="width: 100%;">';
    echo '<thead><tr><th>Name</th><th>Driving Type</th><th>Hometown</th></tr></thead>';
    echo '<tbody>';

    foreach ($attendees as $attendee) {
        echo '<tr>';
        echo '<td>' . esc_html($attendee['holder_name']) . '</td>';
        echo '<td>' . (isset($attendee['attendee_meta']['driving-type']['value']) ? esc_html($attendee['attendee_meta']['driving-type']['value']) : '-') . '</td>';
        echo '<td>' . (isset($attendee['attendee_meta']['hometown']['value']) ? esc_html($attendee['attendee_meta']['hometown']['value']) : '-') . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';

    // Return the buffered content
    return ob_get_clean();
}
