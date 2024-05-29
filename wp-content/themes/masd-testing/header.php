<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package MASD_TESTING
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <!-- Sets the character encoding for the document -->
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <!-- Ensures the website is responsive to different screen sizes -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Profile link for social networking functionality -->
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <!-- Preconnect to Google Fonts to improve loading time -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Link to Google Fonts for Poppins and Merriweather -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <!-- Link to Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/bda91302af.js" crossorigin="anonymous"></script>
    <!-- Link to an older version of Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>


    <?php wp_head(); ?> <!-- Triggers the 'wp_head' action, which allows WordPress and plugins to inject necessary elements into the <head> section. 
        These elements include stylesheets, scripts, meta tags, and other resources required for the theme and plugins to function correctly. -->

</head>

<body <?php body_class(); ?>> <!-- Adds classes to the body tag to help with styling -->

<?php
// Function to display emergency alerts from the main site
function display_main_site_alerts() {
    switch_to_blog(1); // Switch to the main site (blog ID 1)

    // Fetch alerts where the 'active' field value is 1
    $alerts = pods('alert', array(
        'where' => "active.meta_value = 1"
    ));

    $index = 0; // Initialize the index variable to keep track of the number of alerts displayed, assigning a unique ID to each alert.
    // Check if there are any alerts
    if ($alerts->total() > 0) {
        while ($alerts->fetch()) { // Loop through each alert
            // Display each alert with a unique ID and a button to dismiss it
            echo '<div id="alert-' . $index . '" class="emergency-alert" data-index="' . $index . '">';
            echo '<h2>' . esc_html($alerts->display('name')) . '</h2>';
            echo '<p>' . $alerts->field('description') . '</p>';
            echo '<button onclick="dismissAlert(this)">Dismiss</button>';
            echo '</div>';
            $index++;
        }
    }

    restore_current_blog(); // Switch back to the current site
}

// If the current page is the front page, display alerts
if (is_front_page()) {
    display_main_site_alerts();
}
?>

<script>
// JavaScript to handle displaying alerts and dismissing them
document.addEventListener("DOMContentLoaded", function() {
    var alerts = document.querySelectorAll('.emergency-alert');
    if (alerts.length > 0) {
        showAlert(0); // Start by showing the first alert
    }
});

function showAlert(index) {
    var alert = document.getElementById('alert-' + index);
    if (alert) {
        alert.classList.add('active'); // Add 'active' class to the alert to display it
    }
}

function dismissAlert(button) {
    var alertBanner = button.parentElement;
    var currentIndex = parseInt(alertBanner.dataset.index); // Get the index of the current alert from its data attribute and convert it to an integer
    alertBanner.style.opacity = '0'; // Fade out the alert
    setTimeout(function() {
        alertBanner.remove(); // Remove the alert from the DOM after the transition
        // Show the next alert if it exists
        if (document.getElementById('alert-' + (currentIndex + 1))) {
            showAlert(currentIndex + 1);
        }
    }, 500); // This should match the duration of the opacity transition set in css
}
</script>
<!-- end alerts -->

<!-- Section for the site logo and navigation -->
<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="header-inner">
            <div class="logo">
                <!-- Link to the home page with the site logo -->
                <a href="/" class="custom-logo-link" rel="home" aria-current="page">
                    <img fetchpriority="high" width="350" height="155" src="/wp-content/uploads/2024/05/logo_reversed_smallv3.png" class="custom-logo" alt="MASD" decoding="async" srcset="/wp-content/uploads/2024/05/logo_reversed_smallv3.png 350w, /wp-content/uploads/2024/05/logo_reversed_smallv3.png 300w" sizes="(max-width: 350px) 100vw, 350px" />
                    <!-- 
                        fetchpriority="high": This attribute signals to the browser that this image is of high importance, encouraging it to prioritize the image's download to ensure it loads quickly. This is useful for key visuals like logos or hero images that significantly impact the initial user experience.
                        decoding="async": This attribute tells the browser to decode the image asynchronously, which can improve page load times by not blocking the rendering of other content while the image is being decoded.
                        srcset: This attribute provides a list of different image file URLs and their corresponding widths. It allows the browser to choose the best image size to use based on the device's screen size and resolution, improving loading efficiency and performance. 
                    -->
                </a>
            </div>
            <nav>
                <!-- Link to trigger the full-screen menu -->
                <a href="#" id="fullscreen-link"><span class="hamburger-icon">&#9776;</span><span class="menu-button"> MENU</span></a>
                <!-- Link to display the schools subnav -->
                <a href="#" id="schools-link">Schools</a>
                <!-- Link to the careers page -->
                <a href="https://mdek12.tedk12.com/hire/index.aspx" id="normal-link" target="_blank" >Careers</a>

                <!-- Full-screen menu div -->
                <div class="fullscreen-div" id="fullscreen-div">
                    <span class="close-button" id="close-button">&times;</span>

                    <!-- Uncomment this to enable WordPress menu functionality -->
                    <?php
                    /*
                    wp_nav_menu(
                        array(
                            'theme_location' => 'menu-1',
                            'menu_id'        => 'primary-menu',
                        )
                    );
                    */
                    ?>

                    <div class="grid-container">
                        <!-- Include custom Walker Class for Navigation Menu -->
                        <?php
                        $site_title = get_bloginfo('name');
                        require 'links.php'; 
                        /* 
                        if ( $site_title == "Yazoo City High School" ) {
                            require 'links_ychs.php'; 
                        }
                        */
                        ?>  
                    </div>
                </div>

                <script>
                // JavaScript to handle the full-screen menu functionality
                const fullscreenLink = document.getElementById('fullscreen-link');
                const fullscreenDiv = document.getElementById('fullscreen-div');
                const closeButton = document.getElementById('close-button');
                let fullscreenShown = false;

                fullscreenLink.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default action that belongs to the event from occurring.
                    toggleFullScreen();
                });

                closeButton.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent the default action that belongs to the event from occurring.
                    toggleFullScreen();
                });

                function toggleFullScreen() {
                if (!fullscreenShown) { // Check if the fullscreen menu is currently hidden
                    fullscreenDiv.style.display = 'block'; // Show the fullscreen menu
                    fullscreenShown = true; // Update the state to indicate the fullscreen menu is now shown
                } else {
                    fullscreenDiv.style.display = 'none'; // Hide the fullscreen menu
                    fullscreenShown = false; // Update the state to indicate the fullscreen menu is now hidden
                }
            }

                // JavaScript to handle the schools subnav
                document.addEventListener('DOMContentLoaded', function() {
                    const schoolsLink = document.getElementById('schools-link'); // Get the 'Schools' link element
                    const schoolsSubnav = document.querySelector('.schools_subnav'); // Get the schools sub-navigation element

                    // Check if both the 'Schools' link and sub-navigation exist
                    if (schoolsLink && schoolsSubnav) {
                        // Add a click event listener to the 'Schools' link
                        schoolsLink.addEventListener('click', function() {
                            // Toggle the display of the schools sub-navigation
                            if (schoolsSubnav.style.display === 'block') {
                                schoolsSubnav.style.display = 'none'; // Hide the sub-navigation if it's currently displayed
                            } else {
                                schoolsSubnav.style.display = 'block'; // Show the sub-navigation if it's currently hidden
                            }
                        });
                    }
                });

                function isMouseOver(event, element) {
                    // Check if the mouse event's related target (the element that the mouse pointer is coming from or going to)
                    // is either the same as the given element or is contained within the given element.
                    return event.relatedTarget === element || element.contains(event.relatedTarget);
                }

                </script>
            </nav>
        </div>
    </header><!-- #masthead -->

    <!-- Schools sub-navigation menu -->
    <div class="schools_subnav">
        <div class="schools-wrapper">

            <?php
            // Function to display sites classified by regions (classifications are in the admin, Mysites > Network Admin > Site Classifications)
            function display_classified_sites_columns() {
                // Arrays to hold sites by classification
                $humphreys_sites = [];
                $yazoo_sites = [];

                // Retrieve all sites in the multisite network
                $sites = get_sites();
                foreach ($sites as $site) {
                    switch_to_blog($site->blog_id); // Switch to the blog to access its options
                    $classification = get_option('site_classification', 'Unclassified'); // Default to 'Unclassified' if none is set
                    $site_name = get_bloginfo('name');
                    $site_url = get_bloginfo('url');
                    restore_current_blog(); // Switch back to the original blog

                    // Sort sites into their respective arrays
                    if ($classification === 'Humphreys') {
                        $humphreys_sites[] = ['name' => $site_name, 'url' => $site_url];
                    } elseif ($classification === 'Yazoo') {
                        $yazoo_sites[] = ['name' => $site_name, 'url' => $site_url];
                    }
                }

                // HTML Output
                echo '<div class="site-columns">';
                echo '<div class="schools-column humphreys"><h2>Humphreys County Region</h2><ul>';
                foreach ($humphreys_sites as $site) {
                    echo "<li><a href='{$site['url']}'>{$site['name']}</a></li>";
                }
                echo '</ul></div>';
                echo '<div class="schools-column yazoo"><h2>Yazoo City Region</h2><ul>';
                foreach ($yazoo_sites as $site) {
                    echo "<li><a href='{$site['url']}'>{$site['name']}</a></li>";
                }
                echo '</ul></div>';
                echo '</div>';
            }

            // Display the sites in columns
            display_classified_sites_columns();
            ?>

        </div>
    </div>
</div>
