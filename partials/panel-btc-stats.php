<?php
/**
 * BTC Stats Panel Partial
 * 
 * Variables needed:
 * - $btc_price (float)
 * - $first_btc_owned_entry (float)
 */

?>
<div class="ft-card ft-card--accent ft-card--compact h-100 d-flex flex-column">
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <div class="ft-icon-pill">₿</div>
        <div>
            <span class="metric-label">Live Spot</span>
            <?php if ($btc_price): ?>
                <h4 class="mb-0">R<?php echo number_format($btc_price, 2, '.', ','); ?> / BTC</h4>
            <?php else: ?>
                <h4 class="mb-0 text-danger">BTC price unavailable</h4>
                <p class="mb-0">Reconnect data stream to refresh</p>
            <?php endif; ?>
        </div>
    </div>
</div>
