<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #f8f8f8;
    }

    .qty-btn {
        width: 28px;
        height: 28px;
        border: 1px solid #e5e7eb;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        background: #fff;
        transition: background .15s, border-color .15s;
        user-select: none;
        flex-shrink: 0;
    }

    .qty-btn:hover {
        background: #f3f4f6;
        border-color: #9ca3af;
    }

    .qty-btn:disabled {
        opacity: 0.35;
        cursor: not-allowed;
        pointer-events: none;
    }

    .odometer-slot {
        display: inline-block;
        overflow: hidden;
        height: 1.35em;
        vertical-align: middle;
        position: relative;
    }

    .odometer-drum {
        display: flex;
        flex-direction: column;
        /* Transition is set inline via Alpine per-digit logic */
        will-change: transform;
    }

    .odometer-digit {
        height: 1.35em;
        line-height: 1.35em;
        text-align: center;
        display: block;
    }

    /* Color flash transition uses cubic-bezier for smooth emerald↔rose↔default */
    .odometer-wrapper {
        transition: color 0.55s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .voucher-input::placeholder {
        font-size: 13px;
        color: #9ca3af;
    }

    #toast-container {
        position: fixed;
        top: 5.5rem;
        right: 1.5rem;
        z-index: 1000;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        pointer-events: none;
    }

    .toast-msg {
        pointer-events: auto;
        width: 24rem;
        padding: 1.15rem;
        border-radius: 1rem;
        border-width: 1px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
        font-size: 0.875rem;
        font-weight: 500;
        animation: toastSlideIn 0.5s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        transition: opacity 0.4s ease, transform 0.4s ease, margin 0.3s ease;
    }

    .toast-msg.toast-success {
        background: #ecfdf5;
        border-color: #a7f3d0;
        color: #065f46;
    }

    .toast-msg.toast-error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .toast-msg.toast-warning {
        background: #fffbeb;
        border-color: #fde68a;
        color: #92400e;
    }

    .toast-msg.toast-exit {
        opacity: 0 !important;
        transform: translateX(110%) scale(0.95) !important;
    }

    @keyframes toastSlideIn {
        from {
            opacity: 0;
            transform: translateX(100%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
</style>
