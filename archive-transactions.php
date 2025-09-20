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

    $total_invested = !empty($rand_invested_data) ? (float) $rand_invested_data[0] : 0;
    $latest_value = !empty($value_on_luno_data) ? (float) $value_on_luno_data[0] : 0;
    $growth_percentage = ($total_invested > 0 && $latest_value > 0)
        ? (($latest_value - $total_invested) / $total_invested) * 100
        : 0;
    $btc_holdings = $first_btc_owned_entry ? (float) $first_btc_owned_entry : 0;
    $transaction_count = count($purchase_dates);
    $per_person_current_value = $latest_value > 0 ? $latest_value / 10 : 0;
    $btc_per_person = $btc_holdings > 0 ? $btc_holdings / 10 : 0;
    $profit_value = $latest_value - $total_invested;

    $purchase_dates_json = json_encode(array_reverse($purchase_dates));
    $rand_invested_data_json = json_encode(array_reverse($rand_invested_data), JSON_NUMERIC_CHECK);
    $value_on_luno_data_json = json_encode(array_reverse($value_on_luno_data), JSON_NUMERIC_CHECK);
?>

<!-- Google Fonts Ubuntu -->
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    html, body, * {
        font-family: 'Space Grotesk', sans-serif !important;
    }

    body {
        background: radial-gradient(circle at top left, rgba(35, 211, 255, 0.12), transparent 45%),
            radial-gradient(circle at bottom right, rgba(255, 140, 66, 0.18), transparent 40%),
            #050916;
        color: #e8f2ff;
        min-height: 100vh;
    }

    #profit-tracker {
        position: relative;
        max-width: 1200px;
    }

    #profit-tracker::before {
        content: '';
        position: absolute;
        inset: -60px;
        background: linear-gradient(135deg, rgba(35, 211, 255, 0.08), rgba(255, 140, 66, 0.06));
        border-radius: 48px;
        z-index: -2;
        filter: blur(60px);
    }

    .fintech-hero h1 {
        font-weight: 700;
        letter-spacing: -0.02em;
        color: #ffffff;
    }

    .fintech-overview {
        margin-top: 2.5rem;
    }

    .metric-card {
        background: linear-gradient(150deg, rgba(12, 20, 41, 0.95), rgba(10, 16, 34, 0.92) 65%, rgba(255, 140, 66, 0.18));
        border: 1px solid rgba(255, 140, 66, 0.38);
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 24px 40px rgba(8, 11, 24, 0.6);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 32px 60px rgba(20, 13, 8, 0.7);
    }

    .metric-label {
        text-transform: uppercase;
        letter-spacing: 0.14em;
        color: rgba(255, 180, 120, 0.82);
        font-size: 0.95rem;
    }

    .metric-value {
        font-weight: 600;
        margin: 0.35rem 0;
        color: #f6fbff;
        font-size: 2.4rem;
    }

    .metric-trend {
        color: rgba(255, 212, 188, 0.9);
        font-size: 1.05rem;
    }

    .metric-trend.positive {
        color: #4ade80;
    }

    .metric-trend.negative {
        color: #f87171;
    }

    .metric-note {
        color: rgba(255, 214, 190, 0.92);
        font-size: 0.95rem;
        margin-top: 0.35rem;
    }

    .ft-card {
        background: linear-gradient(140deg, rgba(14, 18, 38, 0.95), rgba(9, 14, 28, 0.94) 60%, rgba(255, 140, 66, 0.18));
        border: 1px solid rgba(255, 140, 66, 0.32);
        border-radius: 26px;
        padding: 2.25rem;
        box-shadow: 0 30px 60px rgba(6, 9, 23, 0.55);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .ft-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 38px 80px rgba(20, 12, 6, 0.7);
    }

    .ft-card--accent {
        border-color: rgba(255, 140, 66, 0.48);
        background: linear-gradient(155deg, rgba(18, 24, 44, 0.96), rgba(18, 14, 20, 0.92), rgba(255, 140, 66, 0.24));
    }

    .ft-card--compact {
        padding: 1.75rem;
    }

    .ft-card--canvas {
        padding: 2rem;
        min-height: 420px;
        border-color: rgba(255, 140, 66, 0.3);
    }

    #transactionChart {
        width: 100% !important;
        height: 100% !important;
    }

    .ft-card--table {
        padding: 0;
    }

    .ft-card--table .table-responsive {
        border-radius: 26px;
        overflow: hidden;
    }

    .ft-table th,
    .ft-table td {
        vertical-align: middle;
    }

    .ft-table thead th {
        border-bottom: none;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 600;
        padding: 1rem 1.25rem;
    }

    .ft-table tbody td {
        padding: 1.05rem 1.25rem;
        border-bottom: 1px solid rgba(255, 140, 66, 0.18);
    }

    .ft-table tbody tr:last-child td {
        border-bottom: none;
    }

    .ft-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.02em;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
    }

    .ft-badge--up {
        background: rgba(74, 222, 128, 0.16);
        color: #4ade80;
    }

    .ft-badge--down {
        background: rgba(248, 113, 113, 0.2);
        color: #f87171;
    }

    .ft-link {
        color: #ffb27d;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .ft-link:hover {
        color: #ff9c5c;
    }

    .ft-icon-pill {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        background: linear-gradient(145deg, rgba(255, 196, 132, 0.2), rgba(255, 140, 66, 0.32));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ff9c5c;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    .ft-card h4 {
        font-weight: 600;
        letter-spacing: -0.02em;
        color: #fff7f1;
        font-size: 2.1rem;
    }

    .ft-card p {
        color: rgba(255, 222, 205, 0.8);
        margin-bottom: 0.4rem;
        font-size: 1.05rem;
    }

    .ft-card strong {
        color: #ffffff;
    }

    a {
        color: #ffb27d;
    }

    a:hover {
        color: #ff9c5c;
    }

    .ft-table {
        width: 100%;
        border-collapse: collapse;
        color: rgba(232, 242, 255, 0.92);
        margin-bottom: 0;
    }

    .ft-table thead {
        background: linear-gradient(135deg, rgba(255, 181, 120, 0.38), rgba(255, 140, 66, 0.28));
        color: #e8f2ff;
        border-bottom: none;
    }

    .ft-table tbody tr {
        border-color: rgba(255, 140, 66, 0.14);
    }

    .ft-table tbody tr:hover {
        background-color: rgba(255, 140, 66, 0.16);
    }

    .ft-card--compact .ft-icon-pill {
        width: 56px;
        height: 56px;
    }

    @media (max-width: 767px) {
        .ft-card,
        .metric-card {
            padding: 1.65rem;
        }

        .metric-label {
            font-size: 0.85rem;
        }

        .metric-value {
            font-size: 2rem;
        }

        .metric-trend {
            font-size: 0.95rem;
        }

        .metric-note {
            font-size: 0.85rem;
        }

        .ft-card h4 {
            font-size: 1.8rem;
        }

        .ft-card p {
            font-size: 0.95rem;
        }
    }
</style>

<div class="container py-5 position-relative" id="profit-tracker">

    <div class="mb-4 text-center fintech-hero">
        <h1 class="fw-bold">Vakansie Yes! Fund Performance</h1>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-5 g-4 fintech-overview">
        <div class="col">
            <?php include plugin_dir_path(__FILE__) . 'partials/panel-btc-stats.php'; ?>
        </div>
        <div class="col">
            <div class="metric-card h-100">
                <span class="metric-label">Total Rand Invested</span>
                <p class="metric-value">R<?php echo number_format($total_invested, 2, '.', ','); ?></p>
                <p class="metric-trend">Across <?php echo $transaction_count; ?> transactions</p>
            </div>
        </div>
        <div class="col">
            <div class="metric-card h-100">
                <span class="metric-label">Current Holdings Value</span>
                <p class="metric-value">R<?php echo number_format($latest_value, 2, '.', ','); ?></p>
                <?php $growth_class = $growth_percentage >= 0 ? 'positive' : 'negative'; ?>
                <p class="metric-trend <?php echo $growth_class; ?>">
                    <?php echo $growth_percentage >= 0 ? '+' : ''; ?><?php echo number_format($growth_percentage, 2); ?>% vs invested capital
                </p>
                <p class="metric-note">Split 10 ways: R<?php echo number_format($per_person_current_value, 2, '.', ','); ?> each</p>
            </div>
        </div>
        <div class="col">
            <div class="metric-card h-100">
                <span class="metric-label">Net Profit</span>
                <p class="metric-value">R<?php echo number_format($profit_value, 2, '.', ','); ?></p>
                <?php $profit_class = $profit_value >= 0 ? 'positive' : 'negative'; ?>
                <p class="metric-trend <?php echo $profit_class; ?>">
                    <?php echo $profit_value >= 0 ? '+' : '-'; ?>R<?php echo number_format(abs($profit_value), 2, '.', ','); ?> vs invested
                </p>
            </div>
        </div>
        <div class="col">
            <div class="metric-card h-100">
                <span class="metric-label">BTC Stack</span>
                <p class="metric-value"><?php echo number_format($btc_holdings, 6); ?> BTC</p>
                <p class="metric-note">Split 10 ways: <?php echo number_format($btc_per_person, 8); ?> BTC each</p>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-4 align-items-stretch">
        <div class="col-lg-7">
            <?php include plugin_dir_path(__FILE__) . 'partials/panel-table.php'; ?>
        </div>

        <div class="col-lg-5">
            <?php include plugin_dir_path(__FILE__) . 'partials/panel-chart.php'; ?>
        </div>
    </div>

</div>

<?php
else :
    echo '<div class="container py-5"><p class="alert alert-warning">No transaction data available.</p></div>';
endif;

get_footer();
