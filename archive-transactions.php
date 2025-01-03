<?php
/**
 * The template for displaying archive pages for 'transactions'
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Andries
 */

get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

// Start the Loop
if (have_posts()) : ?>

    <?php 
    // Initialize arrays to store the chart data
    $purchase_dates = [];
    $rand_invested_data = [];
    $value_on_luno_data = [];
    
    $first_btc_owned_entry = null; // Initialize the variable to store the first entry of "AMOUNT OF BTC OWNED" column

    // Start the Loop
    while (have_posts()) : the_post();
        // ACF fields for the current post
        $purchase_date = get_field('purchase_date');
        $rand_amount_invested = get_field('rand_amount_invested');
        $value_on_luno = get_field('value_on_luno');
        $amount_of_btc_owned = get_field('amount_of_btc_owned');

        // Store the first entry of "AMOUNT OF BTC OWNED" column
        if ($first_btc_owned_entry === null) {
            $first_btc_owned_entry = $amount_of_btc_owned;
        }

        // Convert the purchase date to a format that JavaScript can understand (e.g., 'Y-m-d')
        $date = DateTime::createFromFormat('d/m/Y', $purchase_date);
        $formatted_date = $date ? $date->format('Y-m-d') : '';

        // Add the data to our arrays
        $purchase_dates[] = $formatted_date;
        $rand_invested_data[] = (float) $rand_amount_invested; // Cast to float to ensure JSON_NUMERIC_CHECK works
        $value_on_luno_data[] = (float) $value_on_luno;
    endwhile;

    // Reverse the data arrays
    $purchase_dates = array_reverse($purchase_dates);
    $rand_invested_data = array_reverse($rand_invested_data);
    $value_on_luno_data = array_reverse($value_on_luno_data);

    // Encode the reversed data as JSON
    $purchase_dates_json = json_encode($purchase_dates);
    $rand_invested_data_json = json_encode($rand_invested_data, JSON_NUMERIC_CHECK);
    $value_on_luno_data_json = json_encode($value_on_luno_data, JSON_NUMERIC_CHECK);
    ?>

    <div class="transaction-dashboard">
        <div class="container">
            <div class="left">
            <h4><?php echo 'Current BTC Price: R' . number_format($btc_price, 2, '.', ','); ?></h4>

            <?php 
            // Calculate the current value of holdings based on the first entry of "AMOUNT OF BTC OWNED" column
            if ($first_btc_owned_entry !== null) {
                $current_holdings_value = $first_btc_owned_entry * $btc_price;
                echo '<h4>If we sold all our BTC on Luno right now, we would have ' . number_format($current_holdings_value, 2) . ' ZAR</h4>';
                
                // Calculate the value per person
                $value_per_person = $current_holdings_value / 10;
                echo '<h4>If divided equally among 10 people, each person would receive ' . number_format($value_per_person, 2) . ' ZAR</h4>';
            }
            ?>

                <div id="archive-transactions-template-container" class="archive-transactions-template-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Purchase Date</th>
                                <th>Rand Amount Invested</th>
                                <th>Amount of BTC Owned</th>
                                <th>Value on Luno</th>
                                <th>Percentage Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            rewind_posts();
                            while (have_posts()) : the_post();
                                // ACF fields for the current post
                                $purchase_date = get_field('purchase_date');
                                $rand_amount_invested = get_field('rand_amount_invested');
                                $amount_of_btc_owned = get_field('amount_of_btc_owned');
                                $value_on_luno = get_field('value_on_luno');
                                $percentage_increase = $value_on_luno && $rand_amount_invested ? (($value_on_luno - $rand_amount_invested) / $rand_amount_invested) * 100 : 0;
                                $percentage_color = $percentage_increase >= 0 ? 'positive' : 'negative'; // Class based on value
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo get_edit_post_link(); ?>"><?php the_title(); ?></a>
                                    </td>
                                    <td><?php echo esc_html($purchase_date); ?></td>
                                    <td><?php echo esc_html(number_format($rand_amount_invested, 2)); ?> ZAR</td>
                                    <td><?php echo esc_html(number_format($amount_of_btc_owned, 8)); ?> BTC</td>
                                    <td><?php echo esc_html(number_format($value_on_luno, 2)); ?> ZAR</td>
                                    <td class="percentage-increase <?php echo $percentage_color; ?>">
                                        <?php echo esc_html(number_format($percentage_increase, 2)); ?>%
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="right">
                <canvas id="transactionChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

    <?php else :
        get_template_part('template-parts/content', 'none');
    endif; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('transactionChart').getContext('2d');
        var transactionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $purchase_dates_json; ?>,
                datasets: [{
                    label: 'Rand Amount Invested',
                    data: <?php echo $rand_invested_data_json; ?>,
                    borderColor: 'blue',
                    fill: false
                }, {
                    label: 'Value on Luno',
                    data: <?php echo $value_on_luno_data_json; ?>,
                    borderColor: 'red',
                    fill: false
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
    </script>

<?php
get_footer();
?>
