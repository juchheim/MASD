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
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php
function display_main_site_alerts() {
    switch_to_blog(1); // Switch to the main site

    $alerts = pods('alert', array(
        'where' => "active.meta_value = 1"
    ));

    $index = 0;
    if ($alerts->total() > 0) {
        while ($alerts->fetch()) {
            echo '<div id="alert-' . $index . '" class="emergency-alert" data-index="' . $index . '">';
            echo '<h2>' . esc_html($alerts->display('name')) . '</h2>';
            echo '<p>' . $alerts->field('description') . '</p>';
            echo '<button onclick="dismissAlert(this)">Dismiss</button>';
            echo '</div>';
            $index++;
        }
    }

    restore_current_blog(); // Restore to the current child site
}

// Check if we are on the front page of any site within the network
if (is_front_page()) {
    display_main_site_alerts();
}
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var alerts = document.querySelectorAll('.emergency-alert');
    if (alerts.length > 0) {
        showAlert(0); // Start by showing the first alert
    }
});

function showAlert(index) {
    var alert = document.getElementById('alert-' + index);
    if (alert) {
        alert.classList.add('active');
    }
}

function dismissAlert(button) {
    var alertBanner = button.parentElement;
    var currentIndex = parseInt(alertBanner.dataset.index);
    alertBanner.style.opacity = '0';
    setTimeout(function() {
        alertBanner.remove(); // Remove the alert from the DOM after the transition
        if (document.getElementById('alert-' + (currentIndex + 1))) {
            showAlert(currentIndex + 1);
        }
    }, 500); // This should match the duration of the opacity transition
}
</script>


<!-- used to dynamically set the logo link to the main page of the current site -->
<?php 
    $current_blog = get_blog_details();
    $current_site_slug = $current_blog->path;
?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="header-inner">
            <div class="logo">
                <!-- Add your logo here -->
            <!--    <a href="<?php // echo $current_site_slug ?>" class="custom-logo-link" rel="home" aria-current="page"><img fetchpriority="high" width="350" height="155" src="/wp-content/uploads/2024/05/logo_reversed_smallv3.png" class="custom-logo" alt="MASD" decoding="async" srcset="/wp-content/uploads/2024/05/logo_reversed_smallv3.png 350w, /wp-content/uploads/2024/05/logo_reversed_smallv3.png 300w" sizes="(max-width: 350px) 100vw, 350px" /></a> -->
                <a href="/" class="custom-logo-link" rel="home" aria-current="page"><img fetchpriority="high" width="350" height="155" src="/wp-content/uploads/2024/05/logo_reversed_smallv3.png" class="custom-logo" alt="MASD" decoding="async" srcset="/wp-content/uploads/2024/05/logo_reversed_smallv3.png 350w, /wp-content/uploads/2024/05/logo_reversed_smallv3.png 300w" sizes="(max-width: 350px) 100vw, 350px" /></a>
                
            </div>
            <nav>
            
            <!-- Link to trigger the full-screen div -->
            <a href="#" id="fullscreen-link"><span class="hamburger-icon">&#9776;</span><span class="menu-button"> MENU</span></a>
            <a href="#" id="schools-link">Schools</a>
            <a href="https://mdek12.tedk12.com/hire/index.aspx" id="normal-link" target="_blank" >Careers</a>

            <!-- Full-screen div -->
            <div class="fullscreen-div" id="fullscreen-div">
            <span class="close-button" id="close-button">&times;</span>
            
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
                    
                <!-- get nav links for masd -->
                
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

            </div>

            <script>
            // JavaScript to handle the full-screen functionality
            const fullscreenLink = document.getElementById('fullscreen-link');
            const fullscreenDiv = document.getElementById('fullscreen-div');
            const closeButton = document.getElementById('close-button');
            let fullscreenShown = false;

            fullscreenLink.addEventListener('click', function(event) {
                event.preventDefault();
                toggleFullScreen();
            });

            closeButton.addEventListener('click', function(event) {
                event.preventDefault();
                toggleFullScreen();
            });

            function toggleFullScreen() {
                if (!fullscreenShown) {
                fullscreenDiv.style.display = 'block';
                fullscreenShown = true;
                } else {
                fullscreenDiv.style.display = 'none';
                fullscreenShown = false;
                }
            }

            // JavaScript to handle the mouse events for the schools subnav
            document.addEventListener('DOMContentLoaded', function() {
                const schoolsLink = document.getElementById('schools-link');
                const schoolsSubnav = document.querySelector('.schools_subnav');

                if (schoolsLink && schoolsSubnav) {
                    schoolsLink.addEventListener('click', function() {
                        if (schoolsSubnav.style.display === 'block') {  // toggle between block and none
                            schoolsSubnav.style.display = 'none';       // to show/hide the schools subnav
                        } else {
                            schoolsSubnav.style.display = 'block';
                        }
                    });
                }
            });

            function isMouseOver(event, element) {
                return event.relatedTarget === element || element.contains(event.relatedTarget);
            }

            </script>
                

            </nav>
        </div>
    </header><!-- #masthead -->
    <div class="schools_subnav">
        
    <div class="schools-wrapper">

        <?php

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
                restore_current_blog(); // Restore original blog

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

        ?>

        <?php display_classified_sites_columns(); ?>

        </div>

    </div>
</div>
