<?php
/**
 * Chart Panel Partial
 * 
 * Variables needed:
 * - $purchase_dates_json (JSON string)
 * - $rand_invested_data_json (JSON string)
 * - $value_on_luno_data_json (JSON string)
 */
?>

<div class="p-4 bg-light rounded shadow h-100">
    <canvas id="transactionChart" width="400" height="400"></canvas>
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