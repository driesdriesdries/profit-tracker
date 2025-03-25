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

global $btc_price;

$purchase_dates = [];
$rand_invested_data = [];
$value_on_luno_data = [];
$first_btc_owned_entry = null;

if (have_posts()) :

    while (have_posts()) : the_post();
        $purchase_date = get_field('purchase_date');
        $rand_amount_invested = get_field('rand_amount_invested');
        $value_on_luno = get_field('value_on_luno');
        $amount_of_btc_owned = get_field('amount_of_btc_owned');

        if ($first_btc_owned_entry === null && $amount_of_btc_owned) {
            $first_btc_owned_entry = $amount_of_btc_owned;
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

<div class="container py-5" id="profit-tracker">

    <div class="mb-5 text-center">
        <h1 class="fw-bold">Vakansie Yes!</h1>
    </div>

    <div class="row g-4 align-items-stretch">
        <div class="col-lg-6">
            <?php include plugin_dir_path(__FILE__) . 'partials/panel-btc-stats.php'; ?>
        </div>

        <div class="col-lg-6">
            <?php include plugin_dir_path(__FILE__) . 'partials/panel-chart.php'; ?>
        </div>
    </div>

    <!-- Table and Image Panel Row - now 6 / 6 -->
    <div class="row g-4 mt-4 align-items-stretch">
        <div class="col-lg-6">
            <?php include plugin_dir_path(__FILE__) . 'partials/panel-table.php'; ?>
        </div>

        <div class="col-lg-6">
            <div class="p-4 bg-light rounded shadow" id="profit-tracker-image-panel">
                <!-- Background image applied via CSS -->
            </div>
        </div>
    </div>

</div>

<?php
else :
    echo '<div class="container py-5"><p class="alert alert-warning">No transaction data available.</p></div>';
endif;

get_footer();