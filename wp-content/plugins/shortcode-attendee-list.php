<?php
/*
Plugin Name: Attendee List
Description: Display the attendees for a given ticket ID>
Version: 1.0.0
Author: Nic D. Ford
Author URI: https://nicdford.com
 */

function attendee_list_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'ticket_id' => '',
    ), $atts);

    // Query the attendees based on the ticket ID
    $args = array(
        'post_type' => 'attendee',
        'meta_key' => 'ticket_id',
        'meta_value' => $atts['ticket_id'],
    );
    $attendees_query = new WP_Query($args);

    // Check if there are any attendees
    if ($attendees_query->have_posts()) {
        $output = '<ul>';
        while ($attendees_query->have_posts()) {
            $attendees_query->the_post();
            $output .= '<li>' . get_the_title() . '</li>';
        }
        $output .= '</ul>';
    } else {
        $output = 'No attendees found.';
    }

    // Reset the query
    wp_reset_postdata();

    return $output;
}
add_shortcode('attendee_list', 'attendee_list_shortcode');
