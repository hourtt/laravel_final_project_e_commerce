<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>
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
    let _prevTotal = {{ $finalTotal ?? ($discountedTotal ?? ($total ?? 0)) }};

    /*──────────────────────────────────────────────────────────────────
    |  VOUCHER SYSTEM (Multiple / Per-Item)
    |  ─────────────────────────────────────
    |  Supports applying a unique voucher code to each product row.
    ──────────────────────────────────────────────────────────────────*/

    function applyVoucher(productId = null) {
        if (!productId) return;

        const codeEl = document.getElementById('voucher-input-' + productId);
        const errEl = document.getElementById('voucher-error-' + productId);
        const applyBtn = document.getElementById('voucher-apply-btn-' + productId);
        const code = codeEl.value.trim().toUpperCase();

        if (!code) {
            errEl.textContent = 'Enter your voucher code';
            errEl.classList.remove('hidden');
            return;
        }

        errEl.classList.add('hidden');
        applyBtn.disabled = true;
        applyBtn.textContent = '…';

        fetch('{{ route('voucher.apply') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    code,
                    product_id: productId
                }),
            })
            .then(async res => {
                const data = await res.json();
                applyBtn.disabled = false;
                applyBtn.textContent = 'Apply';

                if (!res.ok || !data.success) {
                    errEl.textContent = data.message || 'Invalid.';
                    errEl.classList.remove('hidden');
                    return;
                }

                // UI: Update Item Row
                document.getElementById('voucher-input-row-' + productId).style.display = 'none';
                document.getElementById('voucher-applied-row-' + productId).style.display = 'flex';
                document.getElementById('applied-code-label-' + productId).textContent = data.voucher_code;

                const discountLabel = document.getElementById('voucher-discount-label-' + productId);
                discountLabel.textContent = '-$' + data.discount_amount.toFixed(2) + ' discount';
                discountLabel.classList.remove('hidden');

                // UI: Update Order Summary
                const discountRow = document.getElementById('discount-row');
                const discountDisplay = document.getElementById('discount-display');

                if (data.total_discount > 0) {
                    discountRow.style.display = 'flex';
                    discountDisplay.textContent = '-$' + data.total_discount.toFixed(2) + ' USD';
                }

                // Update grand total odometer
                const direction = data.final_total < _prevTotal ? 'down' : 'up';
                _fireGrandEvent(data.final_total, direction);
                _prevTotal = data.final_total;

                showToast('🎉 ' + data.message, 'success');
                codeEl.value = '';
            })
            .catch(() => {
                errEl.textContent = 'Network error.';
                errEl.classList.remove('hidden');
                applyBtn.disabled = false;
                applyBtn.textContent = 'Apply';
            });
    }

    function removeVoucher(productId = null) {
        if (!productId) return;

        fetch('{{ route('voucher.remove') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId
                }),
            })
            .then(async res => {
                const data = await res.json();

                // UI: Reset Item Row
                document.getElementById('voucher-input-row-' + productId).style.display = 'flex';
                document.getElementById('voucher-applied-row-' + productId).style.display = 'none';
                document.getElementById('voucher-input-' + productId).value = '';
                document.getElementById('voucher-discount-label-' + productId).classList.add('hidden');

                // UI: Update Order Summary
                const discountRow = document.getElementById('discount-row');
                const discountDisplay = document.getElementById('discount-display');

                if (data.total_discount > 0) {
                    discountDisplay.textContent = '-$' + data.total_discount.toFixed(2) + ' USD';
                } else {
                    discountRow.style.display = 'none';
                }

                // Restore grand total odometer
                const direction = data.final_total > _prevTotal ? 'up' : 'down';
                _fireGrandEvent(data.final_total, direction);
                _prevTotal = data.final_total;

                showToast('Voucher removed.', 'warning');
            })
            .catch(() => showToast('Could not remove voucher.', 'error'));
    }

    // Adjust keyboard listeners
    document.addEventListener('DOMContentLoaded', () => {
        // We use inline onkeydown in the blade, so no need for global listener here
        // but we can keep the uppercase transform if needed.
    });

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
                     * Server confirmed. Always correct the odometer and _prevTotal
                     * from server truth to prevent display drift on the Total.
                     * Use serverGrand if returned, otherwise keep the optimistic value.
                     */
                    const serverRow = parseFloat(data.row_subtotal);
                    const serverGrand = parseFloat(data.grand_total);

                    // Sync row odometer if server returned a valid value that differs
                    if (!isNaN(serverRow) && Math.abs(serverRow - newRowTotal) > 0.01) {
                        _fireRowEvent(productId, serverRow, serverRow > newRowTotal ? 'up' : 'down');
                    }

                    // Always sync grand total from server — prevents stale Total display
                    if (!isNaN(serverGrand)) {
                        if (Math.abs(serverGrand - newGrandTotal) > 0.01) {
                            _fireGrandEvent(serverGrand, serverGrand > newGrandTotal ? 'up' : 'down');
                        }
                        _prevTotal = serverGrand;

                        // Also sync the plain Sub Total text display
                        if (subDisplay) {
                            subDisplay.textContent = '$' + serverGrand.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + ' USD';
                        }
                    } else {
                        _prevTotal = newGrandTotal;
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
     |  CHECKOUT BUTTON — fetch fresh ABA params, then open popup
     |
     |  Why we re-fetch before every popup open:
     |  The hash, amount, and req_time are generated at page load. If the
     |  user changes quantity after load, those values become stale. We
     |  get fresh params from payment.prepare (which uses the current cart)
     |  so the ABA KHQR popup always shows the correct, up-to-date total.
     ──────────────────────────────────────────────────────────────────*/
    $(document).ready(function() {
        $('#checkout_button').on('click', function(e) {
            e.preventDefault();

            const btn = $(this);
            const txt = document.getElementById('checkout_text');
            btn.prop('disabled', true);
            if (txt) txt.textContent = 'Processing…';

            // Fetch fresh payment params (hash, amount, tran_id, req_time)
            // so the ABA popup always reflects the current cart total
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
                        btn.prop('disabled', false);
                        if (txt) txt.textContent = 'Checkout Now';
                        return;
                    }

                    // Inject fresh server values into the ABA hidden form fields
                    const fields = ['hash', 'tran_id', 'amount', 'merchant_id', 'req_time',
                        'currency', 'payment_option', 'return_url', 'continue_success_url'
                    ];
                    fields.forEach(f => {
                        const el = document.getElementById(f);
                        if (el && data[f] !== undefined) el.value = data[f];
                    });

                    btn.prop('disabled', false);
                    if (txt) txt.textContent = 'Checkout Now';

                    // Open ABA PayWay KHQR popup — now with up-to-date amount
                    if ($('.payment_option:checked').length > 0) {
                        $('#aba_merchant_request').append($('.payment_option:checked'));
                    }
                    AbaPayway.checkout();
                })
                .catch(() => {
                    showToast('A network error occurred. Please try again.', 'error');
                    btn.prop('disabled', false);
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
