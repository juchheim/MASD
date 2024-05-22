<div class="threeColumn">
    <?php
    $slug = get_post_field('post_name', get_the_ID());
    $pod = pods('staff');
    $params = array(
        'limit' => -1,
        'where' => "department.meta_value = '" . esc_sql($slug) . "'", // the slug has to equal a department name for this pod to display
        'orderby' => 'priority.meta_value DESC, last_name.meta_value ASC'
    );

    $pod->find($params);
        
    if ($pod->total() > 0) {
        while ($pod->fetch()) {
            $image = $pod->display('image');
            $first_name = $pod->display('first_name');
            $last_name = $pod->display('last_name');
            $title = $pod->display('staff_title');
            $email = $pod->display('email');
            $phone_number = $pod->display('phone_number');
            $priority = $pod->field('priority');
            
            // Determine the CSS classes based on priority
            $priority99 = ($priority == 99) ? 'darkblue-div' : '';
            $priority98 = ($priority == 98) ? 'gray-div' : '';
            ?>
            <div class="threeColumnSingle <?php echo esc_attr($priority99); ?> <?php echo esc_attr($priority98); ?>">
                <?php
                if ($image) {
                    echo "<img class='staffPhoto' src='" . esc_url($image) . "' alt='" . esc_attr($first_name . " " . $last_name) . "' />";
                } else {
                    echo "<img class='staffPhoto' src='/wp-content/uploads/2024/04/no_image_available.jpg' alt='No image available' />";
                }

                echo "<h2>" . esc_html($first_name) . " " . esc_html($last_name) . "</h2><hr class='staff-hr'>";
                if ($title) {
                    echo "<p>" . esc_html($title) . "</p>";
                }
                if ($email) {
                    echo "<p><a href='mailto:" . esc_attr($email) . "'>" . esc_html($email) . "</a></p>";
                }
                if ($phone_number) {
                    echo "<p>" . esc_html($phone_number) . "</p>";
                }
                ?>
            </div>
            <?php
        }
    }
    ?>
</div>
