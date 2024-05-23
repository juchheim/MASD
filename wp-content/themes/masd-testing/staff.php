<?php
$slug = get_post_field('post_name', get_the_ID());
$pod = pods('staff');
$params = array(
    'limit' => -1,
    'where' => "department.meta_value = '" . esc_sql($slug) . "'", // the slug has to equal a department name for this pod to display
    'orderby' => 'priority.meta_value DESC, last_name.meta_value ASC'
);

$pod->find($params);

$staff_members = [];
if ($pod->total() > 0) {
    while ($pod->fetch()) {
        $staff_members[] = array(
            'image' => $pod->display('image'),
            'first_name' => $pod->display('first_name'),
            'last_name' => $pod->display('last_name'),
            'title' => $pod->display('staff_title'),
            'email' => $pod->display('email'),
            'phone_number' => $pod->display('phone_number'),
            'priority' => $pod->field('priority')
        );
    }
}

// Determine the column class based on the number of staff members
if (count($staff_members) == 2) {
    echo "<div class='twoColumn'>";
} else {
    echo "<div class='threeColumn'>";
}

if (count($staff_members) == 1) {
    // Output an empty div with the class "empty"
    echo '<div class="threeColumnSingle empty"></div>';
}

foreach ($staff_members as $staff) {
    $priority99 = ($staff['priority'] == 99) ? 'darkblue-div' : '';
    $priority98 = ($staff['priority'] == 98) ? 'red-div' : '';
    ?>
    <div class="threeColumnSingle <?php echo esc_attr($priority99); ?> <?php echo esc_attr($priority98); ?>">
        <?php
        if ($staff['image']) {
            echo "<img class='staffPhoto' src='" . esc_url($staff['image']) . "' alt='" . esc_attr($staff['first_name'] . " " . $staff['last_name']) . "' />";
        } else {
            echo "<img class='staffPhoto' src='/wp-content/uploads/2024/04/no_image_available.jpg' alt='No image available' />";
        }

        echo "<h2>" . esc_html($staff['first_name']) . " " . esc_html($staff['last_name']) . "</h2><hr class='staff-hr'>";
        if ($staff['title']) {
            echo "<p>" . esc_html($staff['title']) . "</p>";
        }
        if ($staff['email']) {
            echo "<p><a href='mailto:" . esc_attr($staff['email']) . "'>" . esc_html($staff['email']) . "</a></p>";
        }
        if ($staff['phone_number']) {
            echo "<p>" . esc_html($staff['phone_number']) . "</p>";
        }
        ?>
    </div>
    <?php
}
?>
</div>
