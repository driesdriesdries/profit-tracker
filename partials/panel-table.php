<?php
/**
 * Transactions Table Partial
 * 
 * Loops through transactions and prints the table rows.
 */
?>
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