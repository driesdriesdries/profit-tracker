<?php
/**
 * BTC Converter Panel Partial
 *
 * Shows inputs for ZAR amounts and converts to BTC using live Luno price.
 * Relies on global $btc_price set by the plugin.
 */

global $btc_price;
?>
<div class="metric-card h-100 d-flex flex-column">
    <span class="metric-label mb-2">BTC ↔ ZAR Converter</span>
    <?php if ($btc_price): ?>
        <p class="metric-note mb-3">Live price: R<?php echo number_format($btc_price, 2, '.', ','); ?> / BTC</p>

        <div class="mb-3">
            <label for="convertZar" class="form-label metric-note mb-1">Amount in ZAR</label>
            <input type="number" step="0.01" min="0" class="form-control form-control-sm" id="convertZar" placeholder="Enter Rand (e.g. 3750)" />
        </div>

        <div>
            <label for="convertBtc" class="form-label metric-note mb-1">Amount in BTC</label>
            <input type="number" step="0.00000001" min="0" class="form-control form-control-sm" id="convertBtc" placeholder="Enter BTC (e.g. 0.0015)" />
        </div>

        <script>
        (function(){
            const price = <?php echo json_encode((float)$btc_price); ?>;
            const zarEl = document.getElementById('convertZar');
            const btcEl = document.getElementById('convertBtc');
            if (!zarEl || !btcEl) return;
            let updating = false;
            const fmtZAR = (x) => isFinite(x) ? Number(x).toFixed(2) : '';
            const fmtBTC = (x) => isFinite(x) ? Number(x).toFixed(8) : '';

            zarEl.addEventListener('input', () => {
                if (updating) return;
                updating = true;
                const zar = parseFloat(zarEl.value);
                const btc = (isFinite(zar) && price > 0) ? zar / price : NaN;
                btcEl.value = fmtBTC(btc);
                updating = false;
            });

            btcEl.addEventListener('input', () => {
                if (updating) return;
                updating = true;
                const btc = parseFloat(btcEl.value);
                const zar = (isFinite(btc) && price > 0) ? btc * price : NaN;
                zarEl.value = fmtZAR(zar);
                updating = false;
            });

            // Optional quick-fill helpers for your monthly split
            // Uncomment to prefill: zarEl.value = '3750'; zarEl.dispatchEvent(new Event('input'));
        })();
        </script>
    <?php else: ?>
        <h4 class="mb-1 text-danger">BTC price unavailable</h4>
        <p class="metric-note mb-0">Cannot convert without live price.</p>
    <?php endif; ?>
</div>
