<?php
/**
 * The template for displaying archive pages for 'transactions'
 * 
 * @package Andries
 */

get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

error_log('Entered archive-transactions.php template');

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

    $purchase_dates_json = json_encode(array_reverse($purchase_dates));
    $rand_invested_data_json = json_encode(array_reverse($rand_invested_data), JSON_NUMERIC_CHECK);
    $value_on_luno_data_json = json_encode(array_reverse($value_on_luno_data), JSON_NUMERIC_CHECK);
?>
<!-- Google Fonts Ubuntu -->
<link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
<style>
    html, body, * {
        font-family: 'Ubuntu', sans-serif !important;
    }
</style>

<div class="container py-5">

    <div class="mb-5 text-center">
        <h1 class="fw-bold">Vakansie Yes!</h1>
    </div>

    <div class="row g-4 align-items-stretch">
        <div class="col-lg-6">
            <div class="p-4 bg-light rounded shadow h-100 d-flex align-items-center justify-content-center text-center">
                <div>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/46/Bitcoin.svg/1200px-Bitcoin.svg.png" alt="Bitcoin Logo" class="img-fluid mb-3" style="max-width: 150px; height: auto;">
                    <?php if ($btc_price): ?>
                        <h4 style="color: #EF8F18;">Current BTC Price: R<?php echo number_format($btc_price, 2, '.', ','); ?></h4>
                    <?php else: ?>
                        <h4 class="text-danger">BTC price unavailable</h4>
                    <?php endif; ?>

                    <?php if ($first_btc_owned_entry !== null && $btc_price):
                        $current_holdings_value = $first_btc_owned_entry * $btc_price;
                        $value_per_person = $current_holdings_value / 10;
                        $btc_per_person = $first_btc_owned_entry / 10;
                    ?>
                        <p class="mt-3">If we sold all our BTC now: <strong><?php echo number_format($current_holdings_value, 2); ?> ZAR</strong></p>
                        <p>Per person (split 10 ways): <strong><?php echo number_format($value_per_person, 2); ?> ZAR</strong></p>
                        <p>BTC per person: <strong><?php echo number_format($btc_per_person, 8); ?> BTC</strong></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="p-4 bg-light rounded shadow h-100">
                <canvas id="transactionChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

    <!-- Full Width Table -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="table-responsive bg-white rounded shadow p-3">
                <table class="table table-striped table-hover align-middle">
                    <thead style="background-color: #EF8F18; color: #000;">
                        <tr>
                            <th>Purchase Date</th>
                            <th>Rand Invested</th>
                            <th>BTC Owned</th>
                            <th>Value on Luno</th>
                            <th>% Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php rewind_posts(); while (have_posts()) : the_post();
                            $purchase_date = get_field('purchase_date');
                            $rand_amount_invested = get_field('rand_amount_invested');
                            $amount_of_btc_owned = get_field('amount_of_btc_owned');
                            $value_on_luno = get_field('value_on_luno');
                            $percentage_increase = ($value_on_luno && $rand_amount_invested) 
                                ? (($value_on_luno - $rand_amount_invested) / $rand_amount_invested) * 100 
                                : 0;
                            $percentage_class = $percentage_increase >= 0 ? 'text-success' : 'text-danger';

                            $date_obj = DateTime::createFromFormat('d/m/Y', $purchase_date);
                            $formatted_display_date = $date_obj ? $date_obj->format('j F Y') : esc_html($purchase_date);
                        ?>
                            <tr>
                                <td><a href="<?php echo get_permalink(); ?>" style="color: #EF8F18;"><?php echo $formatted_display_date; ?></a></td>
                                <td>R<?php echo esc_html(number_format($rand_amount_invested, 2)); ?></td>
                                <td><?php echo esc_html(number_format($amount_of_btc_owned, 8)); ?> BTC</td>
                                <td>R<?php echo esc_html(number_format($value_on_luno, 2)); ?></td>
                                <td class="<?php echo $percentage_class; ?>"><?php echo esc_html(number_format($percentage_increase, 2)); ?>%</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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
                    borderColor: '#000000',
                    fill: false,
                    tension: 0.3
                }, {
                    label: 'Value on Luno',
                    data: <?php echo $value_on_luno_data_json; ?>,
                    borderColor: '#EF8F18',
                    fill: false,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>

<?php
else :
    error_log('No transactions found in archive.');
    echo '<div class="container py-5"><p class="alert alert-warning">No transaction data available.</p></div>';
endif;

get_footer();