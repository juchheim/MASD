<?php
/*
Plugin Name: Google Calendar Events
Description: A plugin to display Google Calendar events using a shortcode.
Version: 1.0
Author: Ernest Juchheim
*/

// Function to fetch and display Google Calendar events
function display_google_calendar_events($atts) {
    $atts = shortcode_atts(array(
        'calendar_id' => '',
        'max_results' => 5,
    ), $atts);

    if (empty($atts['calendar_id'])) {
        return '<p>Please provide the Google Calendar ID.</p>';
    }

    $calendar_id = urlencode($atts['calendar_id']);
    $api_key = 'AIzaSyC6fx9c_ePhFy3DHDeOFB-5iSFUtjBwtwk'; // Hardcoded API key
    $max_results = intval($atts['max_results']);
    $time_min = urlencode(date('c')); // Current date/time in RFC3339 format

    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events?key={$api_key}&maxResults={$max_results}&orderBy=startTime&singleEvents=true&timeMin={$time_min}";

    $response = wp_remote_get($url);

    if (is_wp_error($response)) {
        return '<p>Unable to retrieve events at this time.</p>';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['items'])) {
        return '';
    }

    // Add the headline before the events
    $output = '<div class="headline"><h1>Calendar of Events</h1></div>';
    $output .= '<ul class="google-calendar-events" style="list-style: none; padding-left: 0; text-align: center;">';

    foreach ($data['items'] as $event) {
        $start = new DateTime($event['start']['dateTime'] ?? $event['start']['date']);
        $output .= '<li>';
        $output .= '<h3 class="event-summary">' . esc_html($event['summary']) . '</h3>';
        $output .= '<p class="event-date" style="margin: 0;"><em>' . $start->format('F j, Y, g:i a') . '</em></p>';
        if (!empty($event['location'])) {
            $output .= '<p class="event-location" style="margin: 0;">' . esc_html($event['location']) . '</p>';
        }
        if (!empty($event['description'])) {
            $output .= '<p class="event-description" style="margin: 0;">' . wp_kses_post($event['description']) . '</p>';
        }
        $output .= '</li>';
    }
    $output .= '</ul>';

    return $output;
}

// Register shortcode
function register_google_calendar_events_shortcode() {
    add_shortcode('google_calendar_events', 'display_google_calendar_events');
}

// Hook into WordPress
add_action('init', 'register_google_calendar_events_shortcode');

// Add admin menu item
function google_calendar_events_add_admin_menu() {
    add_menu_page(
        'Google Calendar Events', // Page title
        'Google Calendar Events', // Menu title
        'manage_options', // Capability
        'google_calendar_events', // Menu slug
        'google_calendar_events_admin_page', // Function to display the page
        'dashicons-calendar-alt', // Icon URL
        100 // Position
    );
}
add_action('admin_menu', 'google_calendar_events_add_admin_menu');

// Display admin page content
function google_calendar_events_admin_page() {
    ?>
    <div class="wrap">
        <h1>Google Calendar Events</h1>
        <p>This plugin allows you to display events from a Google Calendar on your WordPress site using a shortcode.</p>
        <h2>How to Use</h2>
        <p>To display Google Calendar events, use the following shortcode:</p>
        <pre>[google_calendar_events calendar_id="YOUR_CALENDAR_ID" max_results="NUMBER_OF_EVENTS"]</pre>
        <p>Replace <code>YOUR_CALENDAR_ID</code> with your Google Calendar ID and <code>NUMBER_OF_EVENTS</code> with the number of events you want to display.</p>
        <h3>Example</h3>
        <pre>[google_calendar_events calendar_id="c_46f1e96c91dde30d948251d704ac1f5ba7e5f104d86eb6e7b254c15e9f093fe7@group.calendar.google.com" max_results="5"]</pre>
        <p>This will display the next 5 events from the specified Google Calendar.</p>
    </div>
    <?php
}
