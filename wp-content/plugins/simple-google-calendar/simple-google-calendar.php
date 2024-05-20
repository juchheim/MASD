<?php
/*
Plugin Name: Simple Google Calendar
Description: A plugin to display Google Calendar events using a shortcode.
Version: 1.0
Author: Ernest Juchheim
*/

// Function to fetch and display Google Calendar events
function display_google_calendar_events($atts) {
    // Set default shortcode attributes and merge with provided ones
    $atts = shortcode_atts(array(
        'calendar_id' => '', // Google Calendar ID
        'max_results' => 5, // Number of events to display
    ), $atts);

    // Check if the calendar ID is provided
    if (empty($atts['calendar_id'])) {
        return '<p>Missing Google Calendar ID.</p>'; // Return an error message if not
    }

    // Encode the calendar ID for use in the URL
    $calendar_id = urlencode($atts['calendar_id']);
    $api_key = 'AIzaSyC6fx9c_ePhFy3DHDeOFB-5iSFUtjBwtwk'; // Hardcoded API key
    $max_results = intval($atts['max_results']); // Ensure max_results is an integer
    $time_min = urlencode(date('c')); // Current date/time in RFC3339 format

    // Construct the URL for the Google Calendar API request
    $url = "https://www.googleapis.com/calendar/v3/calendars/{$calendar_id}/events?key={$api_key}&maxResults={$max_results}&orderBy=startTime&singleEvents=true&timeMin={$time_min}";

    // Fetch the events from the Google Calendar API
    $response = wp_remote_get($url);

    // Check if the request failed
    if (is_wp_error($response)) {
        return '<p>Unable to retrieve events at this time.</p>'; // Return an error message if failed
    }

    // Extract the body content from the HTTP response received from the Google Calendar API
    $body = wp_remote_retrieve_body($response);

    // Decode the JSON-formatted string into a PHP associative array
    // The second parameter 'true' converts the JSON string into an associative array instead of an object
    $data = json_decode($body, true);

    // Check if there are no events
    if (empty($data['items'])) {
        return ''; // Return an empty string if no events
    }

    // Initialize the output variable
    // The $output variable will hold the HTML content that will be returned and displayed on the page.
    // This content includes the headline (if on the home page) and the list of Google Calendar events.
    // We start with an empty string and build the HTML content incrementally as we process the events.
    $output = '';

    // Check if the current page is the home page
    if (is_front_page()) {
        // Initialize the output with a headline if the page is the home page
        $output .= '<div class="headline"><h1>Calendar of Events</h1></div>';
    }

    // Add the opening tag for the list of events
    $output .= '<ul class="google-calendar-events">';

    // Loop through each event and format the output
    foreach ($data['items'] as $event) {
        $start = new DateTime($event['start']['dateTime'] ?? $event['start']['date']); // Get the event start time
        $output .= '<li>';
        $output .= '<h3 class="event-summary">' . esc_html($event['summary']) . '</h3>'; // Event title
        $output .= '<p class="event-date"><em>' . $start->format('F j, Y, g:i a') . '</em></p>'; // Event date and time
        if (!empty($event['location'])) {
            $output .= '<p class="event-location">' . esc_html($event['location']) . '</p>'; // Event location
        }
        if (!empty($event['description'])) {
            $output .= '<p class="event-description">' . wp_kses_post($event['description']) . '</p>'; // Event description
        }
        $output .= '</li>';
    }
    $output .= '</ul>';

    return $output; // Return the formatted event list
}

// Register the shortcode to display Google Calendar events
function register_google_calendar_events_shortcode() {
    add_shortcode('google_calendar_events', 'display_google_calendar_events');
}

// Hook the shortcode registration function into WordPress initialization
add_action('init', 'register_google_calendar_events_shortcode');

// Function to add an admin menu item for the plugin
function google_calendar_events_add_admin_menu() {
    add_menu_page(
        'Google Calendar Events', // Page title
        'Google Calendar Events', // Menu title
        'manage_options', // Capability required to access this menu
        'google_calendar_events', // Menu slug
        'google_calendar_events_admin_page', // Function to display the page content
        'dashicons-calendar-alt', // Icon for the menu
        100 // Position in the menu
    );
}
add_action('admin_menu', 'google_calendar_events_add_admin_menu');

// Function to display the content of the admin page
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
