<?php
/**
 * Plugin Name: Vakansie Yes Profit Tracker
 * Plugin URI: https://github.com/driesdriesdries/profit-tracker
 * Description: Worst Case Paternoster
 * Version: 70
 * Author: Andries Bester
 * Author URI: https://github.com/driesdriesdries/profit-tracker
 */

error_log('Vakansie Yes Profit Tracker plugin loaded.');

// Register the custom post type
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
        'show_in_rest'       => false,
    );

    register_post_type('transactions', $args);
    error_log('Registered custom post type: transactions');
}
add_action('init', 'profit_tracker_register_post_type');

// Template overrides
function profit_tracker_single_template($single_template) {
    global $post;
    if ($post->post_type === 'transactions') {
        $plugin_single_template = plugin_dir_path(__FILE__) . 'single-transactions.php';
        if (file_exists($plugin_single_template)) {
            error_log('Loaded custom single template for transactions.');
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
            error_log('Loaded custom archive template for transactions.');
            return $plugin_archive_template;
        }
    }
    return $archive_template;
}
add_filter('archive_template', 'profit_tracker_archive_template', 99);

// Enqueue Bootstrap 5 and plugin styles
function profit_tracker_enqueue_styles() {
    if (is_singular('transactions') || is_post_type_archive('transactions')) {
        wp_enqueue_style('profit-tracker-style', plugin_dir_url(__FILE__) . 'style.css');
        wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
        error_log('Enqueued profit tracker styles and Bootstrap CSS.');
    }
}
add_action('wp_enqueue_scripts', 'profit_tracker_enqueue_styles');

// Enqueue Chart.js and Bootstrap JS
function profit_tracker_enqueue_scripts() {
    if (is_post_type_archive('transactions')) {
        wp_enqueue_script('chartjs', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
        wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], null, true);
        error_log('Chart.js and Bootstrap JS enqueued.');
    }
}
add_action('wp_enqueue_scripts', 'profit_tracker_enqueue_scripts');

// Admin update notice
function profit_tracker_display_update_notice() {
    if (!current_user_can('manage_options')) {
        return;
    }
    $previous_version = get_option('profit_tracker_version');
    $current_version = '70';
    if ($current_version !== $previous_version) {
        update_option('profit_tracker_version', $current_version);
        ?>
        <div class="notice notice-info is-dismissible">
            <p><?php echo esc_html__('Vakansie Yes Profit Tracker updated to version ' . $current_version . '!', 'textdomain'); ?></p>
        </div>
        <?php
        error_log('Plugin version updated and notice displayed.');
    }
}
add_action('admin_notices', 'profit_tracker_display_update_notice');

// Luno API fetcher with debug
function fetch_btc_price_from_luno() {
    $api_key = get_field('luno_api_key', 'option');
    $api_secret = get_field('luno_api_key_copy', 'option');
    $url = 'https://api.luno.com/api/1/ticker?pair=XBTZAR';

    if (!$api_key || !$api_secret) {
        error_log('Luno API keys missing.');
        return false;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$api_key:$api_secret");

    $response = curl_exec($ch);
    if ($response === false) {
        error_log('Luno API curl error: ' . curl_error($ch));
        curl_close($ch);
        return false;
    }

    curl_close($ch);
    $response_data = json_decode($response);

    if (isset($response_data->last_trade)) {
        $btc_price = (float) $response_data->last_trade;
        error_log('BTC price fetched: ' . $btc_price);
        return $btc_price;
    } else {
        error_log('Luno API response invalid or last_trade missing.');
        return false;
    }
}

// Make BTC price globally available
add_action('init', function() {
    global $btc_price;
    $btc_price = fetch_btc_price_from_luno();
    if (!$btc_price) {
        error_log('BTC price fetch failed or returned empty.');
    }
});