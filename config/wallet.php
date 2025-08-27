<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Wallet Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the wallet system
    |
    */

    // Withdrawal limits
    'withdrawal_limits' => [
        'daily' => 5000000,      // 5M VND per day
        'monthly' => 50000000,   // 50M VND per month
        'min_amount' => 50000,   // Minimum 50K VND
        'max_amount' => 5000000, // Maximum 5M VND per transaction
    ],

    // Processing limits
    'auto_process_limit' => 1000000, // Auto process withdrawals <= 1M VND
    
    // Processing fees
    'processing_fees' => [
        'tier_1' => [
            'max_amount' => 500000,
            'fee' => 5000,
        ],
        'tier_2' => [
            'max_amount' => 2000000,
            'fee' => 10000,
        ],
        'tier_3' => [
            'max_amount' => PHP_INT_MAX,
            'fee' => 15000,
        ],
    ],

    // Deposit limits
    'deposit_limits' => [
        'min_amount' => 10000,      // Minimum 10K VND
        'max_amount' => 10000000,   // Maximum 10M VND per transaction
        'daily' => 50000000,        // 50M VND per day
    ],

    // Transaction timeouts
    'timeouts' => [
        'deposit_timeout' => 15,    // 15 minutes for deposit transactions
        'withdrawal_timeout' => 24, // 24 hours for withdrawal processing
    ],

    // Notification settings
    'notifications' => [
        'admin_withdrawal_threshold' => 1000000, // Notify admin for withdrawals > 1M
        'admin_emails' => [
            env('ADMIN_EMAIL', 'admin@example.com'),
        ],
    ],

    // Bank validation
    'bank_validation' => [
        'account_min_length' => 8,
        'account_max_length' => 20,
        'supported_banks' => [
            'Vietcombank',
            'VietinBank',
            'BIDV',
            'Agribank',
            'Techcombank',
            'MBBank',
            'VPBank',
            'ACB',
            'SHB',
            'Eximbank',
            'Sacombank',
            'TPBank',
            'HDBank',
            'VIB',
            'MSB',
            'OCB',
            'LienVietPostBank',
            'SeABank',
            'VietCapitalBank',
            'SCB',
        ],
    ],

    // Security settings
    'security' => [
        'max_failed_attempts' => 3,
        'lockout_duration' => 30, // minutes
        'require_2fa_amount' => 2000000, // Require 2FA for amounts > 2M
    ],
];
