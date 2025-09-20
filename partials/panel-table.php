<?php
/**
 * Transactions Table Partial
 * 
 * Loops through transactions and prints the table rows.
 */
?>
<div class="ft-card ft-card--table h-100">
    <div class="table-responsive">
        <table class="ft-table align-middle">
            <thead>
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
                    $badge_class = $percentage_increase >= 0 ? 'ft-badge--up' : 'ft-badge--down';
                    $badge_symbol = $percentage_increase >= 0 ? '▲' : '▼';

                    $date_obj = DateTime::createFromFormat('d/m/Y', $purchase_date);
                    $formatted_display_date = $date_obj ? $date_obj->format('j F Y') : esc_html($purchase_date);
                ?>
                    <tr>
                        <td><a class="ft-link" href="<?php echo get_permalink(); ?>"><?php echo $formatted_display_date; ?></a></td>
                        <td>R<?php echo esc_html(number_format($rand_amount_invested, 2)); ?></td>
                        <td><?php echo esc_html(number_format($amount_of_btc_owned, 8)); ?> BTC</td>
                        <td>R<?php echo esc_html(number_format($value_on_luno, 2)); ?></td>
                        <td>
                            <span class="ft-badge <?php echo $badge_class; ?>">
                                <?php echo $badge_symbol; ?>
                                <?php echo esc_html(number_format($percentage_increase, 0)); ?>%
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
