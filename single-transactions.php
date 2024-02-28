<?php
/**
 * The template for displaying all single posts of type 'transactions'
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Andries
 */

get_header(); 

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}
?>

<?php
// Ensure we are on a single transaction post
if (have_posts()) : 
    while (have_posts()) : the_post(); 
        // ACF fields for the current post
        $rand_amount_invested = get_field('rand_amount_invested');
        $amount_of_btc_owned = get_field('amount_of_btc_owned');
        $value_on_luno = get_field('value_on_luno');
        $date = get_the_date(); // Get the date of the post
        $post_id = get_the_ID(); // Get the ID of the current post
?>
        <div class="transactions-template-container" style="display: flex;">
            <div class="left" style="flex: 1; padding: 10px;">
                <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: black;">
                    <h1><?php the_title(); ?></h1>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Date</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Rand Amount Invested</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Amount of BTC Owned</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2;">Value on Luno</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="background-color: #f9f9f9;">
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;"><?php echo esc_html($date); ?></td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;"><?php echo esc_html(number_format($rand_amount_invested, 2)); ?> ZAR</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;"><?php echo esc_html(number_format($amount_of_btc_owned, 8)); ?> BTC</td>
                                <td style="border: 1px solid #ddd; padding: 8px; text-align: left;"><?php echo esc_html(number_format($value_on_luno, 2)); ?> ZAR</td>
                            </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="right" style="flex: 1; padding: 10px;">
                <!-- You can add other content here, such as a sidebar or additional information -->
            </div>
        </div>

<?php 
    endwhile; 
endif;

get_footer(); 
?>
