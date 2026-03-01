<?php 

return [
    'transaction_url' => env('PAYWAY_TRANSACTION_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/check-transaction-2'),
    'api_url' => env('PAYWAY_API_URL', 'https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase'),
    'public_key' => env('PAYWAY_PUBLIC_KEY', 'f672d4371ba168831801e2de5dbd36122cf2bde2'),
    'merchant_id' => env('PAYWAY_MERCHANT_ID', 'ec463540'),
];  