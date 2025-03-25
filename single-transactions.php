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


// Ensure we are on a single transaction post
if (have_posts()) :
    while (have_posts()) : the_post();
        // ACF fields for the current post
        $rand_amount_invested = get_field('rand_amount_invested');
        $amount_of_btc_owned = get_field('amount_of_btc_owned');
        $value_on_luno = get_field('value_on_luno');
        $date = get_the_date();
        $post_id = get_the_ID();

        ?>
        
        <div class="transactions-template-container">
            <div class="left">
                <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: black;">
                    <h1><?php the_title(); ?></h1>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Rand Amount Invested</th>
                                <th>Amount of BTC Owned</th>
                                <th>Value on Luno</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo esc_html($date); ?></td>
                                <td><?php echo esc_html(number_format($rand_amount_invested, 2)); ?> ZAR</td>
                                <td><?php echo esc_html(number_format($amount_of_btc_owned, 8)); ?> BTC</td>
                                <td><?php echo esc_html(number_format($value_on_luno, 2)); ?> ZAR</td>
                            </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="right">
                <!-- Optional sidebar or additional data -->
            </div>
        </div>

    <?php
    endwhile;
else :
    echo '<p>No transaction data available.</p>';
endif;

get_footer();