<?php
/**
 * Plugin Name: Vakansie Yes Profit Tracker
 * Plugin URI: https://github.com/driesdriesdries/profit-tracker
 * Description: Worst Case Paternoster
 * Version: 71
 * Author: Andries Bester
 * Author URI: https://github.com/driesdriesdries/profit-tracker
 */

// Register the custom post type
function profit_tracker_register_post_type() {
    $labels = array(
        'name' => _x('Transactions', 'Post type general name', 'textdomain'),
        'singular_name' => _x('Transaction', 'Post type singular name', 'textdomain'),
        'menu_name' => _x('Transactions', 'Admin Menu text', 'textdomain'),
        'name_admin_bar' => _x('Transaction', 'Add New on Toolbar', 'textdomain'),
        'add_new' => __('Add New', 'textdomain'),
        'add_new_item' => __('Add New Transaction', 'textdomain'),
        'new_item' => __('New Transaction', 'textdomain'),
        'edit_item' => __('Edit Transaction', 'textdomain'),
        'view_item' => __('View Transaction', 'textdomain'),
        'all_items' => __('All Transactions', 'textdomain'),
        'search_items' => __('Search Transactions', 'textdomain'),
        'parent_item_colon' => __('Parent Transactions:', 'textdomain'),
        'not_found' => __('No transactions found.', 'textdomain'),
        'not_found_in_trash' => __('No transactions found in Trash.', 'textdomain'),
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'transactions'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => null,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        'show_in_rest' => false,
    );

    register_post_type('transactions', $args);
}
add_action('init', 'profit_tracker_register_post_type');

// Template overrides
function profit_tracker_single_template($single_template) {
    global $post;
    if ($post->post_type === 'transactions') {
        $plugin_single_template = plugin_dir_path(__FILE__) . 'single-transactions.php';
        if (file_exists($plugin_single_template)) {
            return $plugin_single_template;
        }
    }
    return $single_template;
}
add_filter('single_template', 'profit_tracker_single_template', 99);

function profit_tracker_archive_template($archive_template) {
    if (is_post_type_archive('transactions')) {
        $plugin_archive_template = plugin_dir_path(__FILE__) . 'archive-transactions.php';
        if (file_exists($plugin_archive_template)) {
            return $plugin_archive_template;
        }
    }
    return $archive_template;
}
add_filter('archive_template', 'profit_tracker_archive_template', 99);

// Enqueue styles with correct order: Bootstrap first, then your plugin styles
function profit_tracker_enqueue_styles() {
    if (is_singular('transactions') || is_post_type_archive('transactions')) {
        // Load Bootstrap first
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
        // Then your styles to ensure your overrides apply
        wp_enqueue_style('profit-tracker-style', plugin_dir_url(__FILE__) . 'style.css', array('bootstrap-css'));
    }
}
add_action('wp_enqueue_scripts', 'profit_tracker_enqueue_styles');

// Enqueue Chart.js and Bootstrap JS
function profit_tracker_enqueue_scripts() {
    if (is_post_type_archive('transactions')) {
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], null, true);
    }
}
add_action('wp_enqueue_scripts', 'profit_tracker_enqueue_scripts');

// Luno API fetcher
function fetch_btc_price_from_luno() {
    $api_key = get_field('luno_api_key', 'option');
    $api_secret = get_field('luno_api_key_copy', 'option');
    $url = 'https://api.luno.com/api/1/ticker?pair=XBTZAR';

    if (!$api_key || !$api_secret) {
        return false;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$api_key:$api_secret");
    $response = curl_exec($ch);
    curl_close($ch);

    $response_data = json_decode($response);

    if (isset($response_data->last_trade)) {
        return (float) $response_data->last_trade;
    }

    return false;
}

// Make BTC price globally available
add_action('init', function() {
    global $btc_price;
    $btc_price = fetch_btc_price_from_luno();
});

// Add Meta Box with "View Dashboard" button in the Transaction post admin screen
function profit_tracker_add_dashboard_button_meta_box() {
    add_meta_box(
        'profit_tracker_dashboard_button',
        __('Dashboard', 'textdomain'),
        'profit_tracker_render_dashboard_button',
        'transactions',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'profit_tracker_add_dashboard_button_meta_box');

function profit_tracker_render_dashboard_button($post) {
    $dashboard_url = get_post_type_archive_link('transactions');
    if ($dashboard_url) {
        echo '<a href="' . esc_url($dashboard_url) . '" target="_blank" class="button" style="background-color: #dc3545; color: #fff; padding: 8px 12px; border-radius: 4px; text-decoration: none; display: inline-block;">View Dashboard</a>';
    } else {
        echo '<p><strong>Error:</strong> Dashboard link unavailable.</p>';
    }
}
?>