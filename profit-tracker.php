<?php
/**
 * Plugin Name: Vakansie Yes Profit Tracker
 * Plugin URI: http://andriesbester.com/profit-tracker
 * Description: Worst Case Paternoster
 * Version: 666
 * Author: Andries Bester
 * Author URI: http://andriesbester.com
 */

 function profit_tracker_register_post_type() {
    $labels = array(
        'name'                  => _x('Transactions', 'Post type general name', 'textdomain'),
        'singular_name'         => _x('Transaction', 'Post type singular name', 'textdomain'),
        'menu_name'             => _x('Transactions', 'Admin Menu text', 'textdomain'),
        'name_admin_bar'        => _x('Transaction', 'Add New on Toolbar', 'textdomain'),
        'add_new'               => __('Add New', 'textdomain'),
        'add_new_item'          => __('Add New Transaction', 'textdomain'),
        'new_item'              => __('New Transaction', 'textdomain'),
        'edit_item'             => __('Edit Transaction', 'textdomain'),
        'view_item'             => __('View Transaction', 'textdomain'),
        'all_items'             => __('All Transactions', 'textdomain'),
        'search_items'          => __('Search Transactions', 'textdomain'),
        'parent_item_colon'     => __('Parent Transactions:', 'textdomain'),
        'not_found'             => __('No transactions found.', 'textdomain'),
        'not_found_in_trash'    => __('No transactions found in Trash.', 'textdomain'),
        'featured_image'        => _x('Transaction Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'textdomain'),
        'set_featured_image'    => _x('Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'remove_featured_image' => _x('Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'use_featured_image'    => _x('Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'textdomain'),
        'archives'              => _x('Transaction archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'textdomain'),
        'insert_into_item'      => _x('Insert into transaction', 'Overrides the “Insert into post”/“Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'textdomain'),
        'uploaded_to_this_item' => _x('Uploaded to this transaction', 'Overrides the “Uploaded to this post”/“Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'textdomain'),
        'filter_items_list'     => _x('Filter transactions list', 'Screen reader text for the filter links heading on the post type listing screen. Added in 4.4', 'textdomain'),
        'items_list_navigation' => _x('Transactions list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Added in 4.4', 'textdomain'),
        'items_list'            => _x('Transactions list', 'Screen reader text for the items list heading on the post type listing screen. Added in 4.4', 'textdomain'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'transactions'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest'       => false, // Disable Gutenberg editor support
    );

    register_post_type('transactions', $args);
}
add_action('init', 'profit_tracker_register_post_type');

function profit_tracker_single_template($single_template) {
    global $post;

    if ($post->post_type == 'transactions') {
        $plugin_single_template = plugin_dir_path(__FILE__) . 'single-transactions.php';
        if (file_exists($plugin_single_template)) {
            return $plugin_single_template;
        }
    }

    return $single_template;
}
add_filter('single_template', 'profit_tracker_single_template');

// New function to filter the archive template
function profit_tracker_archive_template($archive_template) {
    global $post;

    if (is_post_type_archive('transactions')) {
        $plugin_archive_template = plugin_dir_path(__FILE__) . 'archive-transactions.php';
        if (file_exists($plugin_archive_template)) {
            return $plugin_archive_template;
        }
    }

    return $archive_template;
}
add_filter('archive_template', 'profit_tracker_archive_template');

function profit_tracker_enqueue_styles() {
    if (is_singular('transactions') || is_post_type_archive('transactions')) {
        wp_enqueue_style('profit-tracker-style', plugin_dir_url(__FILE__) . 'style.css');
    }
}

add_action('wp_enqueue_scripts', 'profit_tracker_enqueue_styles');


// Additional plugin code...

function enqueue_chartjs() {
    if (is_post_type_archive('transactions')) { // Only on the transactions archive page
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_chartjs');
