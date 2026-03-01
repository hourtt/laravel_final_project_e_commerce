<script>
    /*──────────────────────────────────────────────────────────────────
     |  CHECKOUT SCRIPTS
     |
     |  changeQty() strategy: OPTIMISTIC UPDATE
     |  ─────────────────────────────────────────
     |  1. Client immediately calculates the new totals (price × qty)
     |  2. Odometer events fire INSTANTLY — zero perceived delay
     |  3. AJAX runs in background to sync the Laravel session
     |  4. If server rejects (stock, etc.) → roll the UI back
     |  5. If server succeeds → optionally correct from truth values
     |
     |  This keeps the animation snappy while the backend stays
     |  the authoritative source of truth.
     ──────────────────────────────────────────────────────────────────*/

    /* Track grand total in JS so we can compute deltas client-side */
    let _prevTotal = {{ $finalTotal ?? 0 }};

    /* ── button state ── */
    function syncButtonStates(productId) {
        const qtyEl = document.getElementById('qty-' + productId);
        const btnPlus = document.getElementById('btn-plus-' + productId);
        const btnMinus = document.getElementById('btn-minus-' + productId);
        if (!qtyEl) return;

        const qty = parseInt(qtyEl.textContent, 10);
        const min = parseInt(qtyEl.dataset.min ?? 1, 10);
        const max = parseInt(qtyEl.dataset.max ?? Infinity, 10);

        if (btnMinus) btnMinus.disabled = (qty <= min);
        if (btnPlus) btnPlus.disabled = (qty >= max);
    }

    /* ── helpers ── */
    function _fireRowEvent(productId, total, direction) {
        window.dispatchEvent(new CustomEvent('row-price-updated-' + productId, {
            detail: {
                total,
                direction
            }
        }));
    }

    function _fireGrandEvent(total, direction) {
        window.dispatchEvent(new CustomEvent('price-updated', {
            detail: {
                total,
                direction
            }
        }));
    }

    /* ── main qty handler ── */
    function changeQty(productId, delta) {
        const qtyEl = document.getElementById('qty-' + productId);
        const btnPlus = document.getElementById('btn-plus-' + productId);
        const btnMinus = document.getElementById('btn-minus-' + productId);

        const currentQty = parseInt(qtyEl.textContent, 10);
        const unitPrice = parseFloat(qtyEl.dataset.price);
        const min = parseInt(qtyEl.dataset.min ?? 1, 10);
        const max = parseInt(qtyEl.dataset.max ?? Infinity, 10);
        const newQty = currentQty + delta;

        /* ── client-side boundary guard ── */
        if (newQty < min) {
            showToast('Minimum quantity is 1.', 'warning');
            return;
        }
        if (newQty > max) {
            showToast(`Only ${max} item${max === 1 ? '' : 's'} available in stock.`, 'warning');
            return;
        }

        /* ── compute new totals client-side ── */
        const oldRowTotal = unitPrice * currentQty;
        const newRowTotal = unitPrice * newQty;
        const oldGrandTotal = _prevTotal;
        const newGrandTotal = oldGrandTotal + (newRowTotal - oldRowTotal);
        const direction = delta > 0 ? 'up' : 'down';

        /* ──────────────────────────────────────────────────
         |  OPTIMISTIC UPDATE — fire odometers NOW, before
         |  the network request even starts. The UI feels
         |  instant. We'll roll back if the server says no.
         ──────────────────────────────────────────────────*/
        qtyEl.textContent = newQty;
        _fireRowEvent(productId, newRowTotal, direction);
        _fireGrandEvent(newGrandTotal, direction);
        _prevTotal = newGrandTotal;

        /* Update Sub Total text instantly (plain span, not odometer) */
        const subDisplay = document.getElementById('subtotal-display');
        if (subDisplay) {
            subDisplay.textContent = '$' + newGrandTotal.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' USD';
        }

        syncButtonStates(productId);

        /* Disable buttons just during flight to prevent double-submit */
        if (btnPlus) btnPlus.disabled = true;
        if (btnMinus) btnMinus.disabled = true;

        /* ── background AJAX — sync the session & validate stock ── */
        fetch('{{ route('cart.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: newQty
                }),
            })
            .then(async res => {
                const data = await res.json();

                if (!res.ok || !data.success) {
                    /* ── ROLLBACK ── */
                    qtyEl.textContent = currentQty;

                    /* Update stock cap if the server's truth differs */
                    if (data.available_stock !== undefined) {
                        qtyEl.dataset.max = data.available_stock;
                    }

                    /* Roll back odometers and subtotal display */
                    const rollDirection = direction === 'up' ? 'down' : 'up';
                    _fireRowEvent(productId, oldRowTotal, rollDirection);
                    _fireGrandEvent(oldGrandTotal, rollDirection);
                    _prevTotal = oldGrandTotal;
                    if (subDisplay) {
                        subDisplay.textContent = '$' + oldGrandTotal.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' USD';
                    }
                    showToast(data.message || 'Error updating cart.', 'error');
                } else {
                    /*
                     * Server confirmed. Correct from server-truth values in case
                     * the client calculation drifted (floating-point, concurrency).
                     * Only re-fire if there's a meaningful difference (> 0.01).
                     */
                    const serverRow = parseFloat(data.row_subtotal) || newRowTotal;
                    const serverGrand = parseFloat(data.grand_total) || newGrandTotal;

                    if (Math.abs(serverRow - newRowTotal) > 0.01) {
                        _fireRowEvent(productId, serverRow, serverRow > newRowTotal ? 'up' : 'down');
                    }
                    if (Math.abs(serverGrand - newGrandTotal) > 0.01) {
                        _fireGrandEvent(serverGrand, serverGrand > newGrandTotal ? 'up' : 'down');
                        _prevTotal = serverGrand;
                    }
                }

                syncButtonStates(productId);
            })
            .catch(err => {
                /* ── ROLLBACK on network failure ── */
                qtyEl.textContent = currentQty;
                const rollDirection = direction === 'up' ? 'down' : 'up';
                _fireRowEvent(productId, oldRowTotal, rollDirection);
                _fireGrandEvent(oldGrandTotal, rollDirection);
                _prevTotal = oldGrandTotal;

                showToast('A network error occurred. Please try again.', 'error');
                console.error('Cart update error:', err);
                syncButtonStates(productId);
            });
    }

    /* ── init button states on page load ── */
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[id^="qty-"]').forEach(el => {
            syncButtonStates(el.id.replace('qty-', ''));
        });
    });

    /*──────────────────────────────────────────────────────────────────
     |  CHECKOUT BUTTON — fetch fresh ABA hash just before submit
     ──────────────────────────────────────────────────────────────────*/
    document.addEventListener('DOMContentLoaded', () => {
        const checkoutBtn = document.getElementById('checkout_button');
        if (!checkoutBtn) return;

        checkoutBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const btn = this;
            const txt = document.getElementById('checkout_text');
            btn.disabled = true;
            if (txt) txt.textContent = 'Processing…';

            fetch('{{ route('payment.prepare') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        showToast(data.error || 'Failed to prepare payment. Please try again.',
                            'error');
                        btn.disabled = false;
                        if (txt) txt.textContent = 'Checkout Now';
                        return;
                    }

                    /* Inject server-fresh values into all ABA hidden fields */
                    const fields = ['hash', 'tran_id', 'amount', 'merchant_id', 'req_time',
                        'currency', 'payment_option', 'return_url', 'continue_success_url'
                    ];
                    fields.forEach(f => {
                        const el = document.getElementById(f);
                        if (el && data[f] !== undefined) el.value = data[f];
                    });

                    document.getElementById('aba_merchant_request').submit();
                })
                .catch(() => {
                    showToast('A network error occurred. Please try again.', 'error');
                    btn.disabled = false;
                    if (txt) txt.textContent = 'Checkout Now';
                });
        });
    });

    /*──────────────────────────────────────────────────────────────────
     |  TOAST SYSTEM
     ──────────────────────────────────────────────────────────────────*/
    function showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-msg toast-${type}`;

        const icons = {
            success: '✓',
            error: '⚠',
            warning: 'ℹ'
        };
        const icon = icons[type] ?? 'ℹ';

        toast.innerHTML = `
            <span class="mt-0.5 text-lg leading-none flex-shrink-0">${icon}</span>
            <div class="flex-1 leading-snug">${message}</div>
            <button type="button"
                    class="mt-0.5 opacity-50 hover:opacity-100 transition-opacity ml-1 flex-shrink-0"
                    onclick="removeToast(this.parentElement)">✕</button>
        `;

        container.appendChild(toast);
        setTimeout(() => removeToast(toast), 4000);
    }

    function removeToast(toast) {
        if (!toast) return;
        toast.classList.add('toast-exit');
        toast.addEventListener('transitionend', () => toast.remove(), {
            once: true
        });
    }

    /* Display server-side flash messages as toasts */
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.server-toast').forEach(el => {
            showToast(el.textContent.trim(), el.dataset.type ?? 'success');
        });
    });
</script>
