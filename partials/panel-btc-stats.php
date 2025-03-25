<?php
/**
 * BTC Stats Panel Partial
 * 
 * Variables needed:
 * - $btc_price (float)
 * - $first_btc_owned_entry (float)
 */

?>
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