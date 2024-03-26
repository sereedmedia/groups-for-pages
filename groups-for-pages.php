<?php
/**
 * Plugin Name: Groups for Pages
 * Description: A plugin to display WordPress pages in the admin grouped by a custom taxonomy.
 * Version: 1.0
 * Author: DevBranch Crew & ChattyG
 */

// Register the custom taxonomy.
function my_custom_taxonomy() {
    register_taxonomy(
        'page_group',
        'page',
        array(
            'label' => __('Page Groups'),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'page_group'),
        )
    );
}
add_action('init', 'my_custom_taxonomy');

// Add a new menu item to the admin.
function my_plugin_menu() {
    add_menu_page(
        'Grouped Pages',
        'Grouped Pages',
        'manage_options',
        'my-plugin-grouped-pages',
        'my_grouped_pages_display_callback'
    );
}
add_action('admin_menu', 'my_plugin_menu');

// The callback function to display grouped pages.
function my_grouped_pages_display_callback() {
    $terms = get_terms('page_group', array('hide_empty' => false));
    
    echo '<div class="wrap"><h1>Grouped Pages</h1>';
    foreach ($terms as $term) {
        echo '<h2>' . esc_html($term->name) . '</h2>';
        
        // Custom query to fetch pages associated with the current term.
        $args = array(
            'post_type' => 'page',
            'posts_per_page' => -1, // Retrieve all pages
            'tax_query' => array(
                array(
                    'taxonomy' => 'page_group',
                    'field'    => 'term_id',
                    'terms'    => $term->term_id,
                ),
            ),
        );
        
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            echo '<ul>';
            while ($query->have_posts()) {
                $query->the_post();
                echo '<li>' . get_the_title() . ' - <a href="' . get_edit_post_link() . '">Edit Page</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No pages found in this group.</p>';
        }
        
        // Reset post data to avoid conflicts with other queries.
        wp_reset_postdata();
    }
    echo '</div>';
}
