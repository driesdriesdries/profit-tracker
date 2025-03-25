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

error_log('Entered archive-transactions.php template');

// Fetch latest BTC price from global scope if available
global $btc_price;
if (!$btc_price) {
    error_log('BTC price not set globally.');
}

$purchase_dates = [];
$rand_invested_data = [];
$value_on_luno_data = [];
$first_btc_owned_entry = null;

if (have_posts()) :
    error_log('Transactions posts found, processing loop.');

    while (have_posts()) : the_post();
        $purchase_date = get_field('purchase_date');
        $rand_amount_invested = get_field('rand_amount_invested');
        $value_on_luno = get_field('value_on_luno');
        $amount_of_btc_owned = get_field('amount_of_btc_owned');

        // Log each entry
        error_log("Processing post ID: " . get_the_ID());
        error_log("Purchase Date: $purchase_date, Invested: $rand_amount_invested, Luno Value: $value_on_luno, BTC Owned: $amount_of_btc_owned");

        if ($first_btc_owned_entry === null && $amount_of_btc_owned) {
            $first_btc_owned_entry = $amount_of_btc_owned;
            error_log("First BTC owned entry recorded: $first_btc_owned_entry");
        }

        $date_obj = DateTime::createFromFormat('d/m/Y', $purchase_date);
        $formatted_date = $date_obj ? $date_obj->format('Y-m-d') : '';

        $purchase_dates[] = $formatted_date;
        $rand_invested_data[] = (float) $rand_amount_invested;
        $value_on_luno_data[] = (float) $value_on_luno;
    endwhile;

    // Prepare data for the chart
    $purchase_dates_json = json_encode(array_reverse($purchase_dates));
    $rand_invested_data_json = json_encode(array_reverse($rand_invested_data), JSON_NUMERIC_CHECK);
    $value_on_luno_data_json = json_encode(array_reverse($value_on_luno_data), JSON_NUMERIC_CHECK);

    ?>
    <div class="transaction-dashboard">
        <div class="container">
            <div class="left">
                <?php if ($btc_price): ?>
                    <h4><?php echo 'Current BTC Price: R' . number_format($btc_price, 2, '.', ','); ?></h4>
                <?php else: ?>
                    <h4>BTC price unavailable</h4>
                <?php endif; ?>

                <?php
                if ($first_btc_owned_entry !== null && $btc_price):
                    $current_holdings_value = $first_btc_owned_entry * $btc_price;
                    $value_per_person = $current_holdings_value / 10;
                    ?>
                    <h4>If we sold all our BTC now: <?php echo number_format($current_holdings_value, 2); ?> ZAR</h4>
                    <h4>Per person (split 10 ways): <?php echo number_format($value_per_person, 2); ?> ZAR</h4>
                <?php endif; ?>

                <div id="archive-transactions-template-container" class="archive-transactions-template-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Purchase Date</th>
                                <th>Rand Invested</th>
                                <th>BTC Owned</th>
                                <th>Value on Luno</th>
                                <th>% Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php rewind_posts(); ?>
                            <?php while (have_posts()) : the_post();
                                $purchase_date = get_field('purchase_date');
                                $rand_amount_invested = get_field('rand_amount_invested');
                                $amount_of_btc_owned = get_field('amount_of_btc_owned');
                                $value_on_luno = get_field('value_on_luno');
                                $percentage_increase = ($value_on_luno && $rand_amount_invested) 
                                    ? (($value_on_luno - $rand_amount_invested) / $rand_amount_invested) * 100 
                                    : 0;
                                $percentage_color = $percentage_increase >= 0 ? 'positive' : 'negative';
                                ?>
                                <tr>
                                    <td><a href="<?php echo get_edit_post_link(); ?>"><?php the_title(); ?></a></td>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Rendering Chart.js graph');
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
else :
    error_log('No transactions found in archive.');
    echo '<p>No transaction data available.</p>';
endif;

get_footer();