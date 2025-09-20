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

<div class="ft-card ft-card--canvas h-100">
    <canvas id="transactionChart" width="400" height="400"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('transactionChart').getContext('2d');
        var gradientInvested = ctx.createLinearGradient(0, 0, 0, 400);
        gradientInvested.addColorStop(0, 'rgba(96, 231, 255, 0.45)');
        gradientInvested.addColorStop(1, 'rgba(96, 231, 255, 0.05)');

        var gradientValue = ctx.createLinearGradient(0, 0, 0, 400);
        gradientValue.addColorStop(0, 'rgba(255, 162, 89, 0.5)');
        gradientValue.addColorStop(1, 'rgba(255, 162, 89, 0.08)');

        var transactionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo $purchase_dates_json; ?>,
                datasets: [{
                    label: 'Rand Amount Invested',
                    data: <?php echo $rand_invested_data_json; ?>,
                    borderColor: '#60e7ff',
                    backgroundColor: gradientInvested,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: '#101b3f'
                }, {
                    label: 'Value on Luno',
                    data: <?php echo $value_on_luno_data_json; ?>,
                    borderColor: '#ff8c42',
                    backgroundColor: gradientValue,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 3,
                    pointRadius: 3,
                    pointBackgroundColor: '#101b3f'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: 'rgba(232, 242, 255, 0.7)',
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(7, 12, 28, 0.92)',
                        borderColor: 'rgba(96, 231, 255, 0.35)',
                        borderWidth: 1,
                        titleColor: '#ffffff',
                        bodyColor: 'rgba(232, 242, 255, 0.9)',
                        padding: 12,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: 'rgba(200, 214, 255, 0.55)'
                        },
                        grid: {
                            color: 'rgba(76, 93, 148, 0.2)'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: 'rgba(200, 214, 255, 0.55)'
                        },
                        grid: {
                            color: 'rgba(76, 93, 148, 0.2)'
                        }
                    }
                }
            }
        });
    });
</script>
