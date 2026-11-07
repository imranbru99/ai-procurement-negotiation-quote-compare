<?php

return [
    'locales' => [
        'en' => ['name' => 'English', 'rtl' => false],
        'bn' => ['name' => 'বাংলা', 'rtl' => false],
        'ar' => ['name' => 'العربية', 'rtl' => true],
        'hi' => ['name' => 'हिन्दी', 'rtl' => false],
        'es' => ['name' => 'Español', 'rtl' => false],
        'fr' => ['name' => 'Français', 'rtl' => false],
        'zh' => ['name' => '中文', 'rtl' => false],
    ],

    'default_locale' => 'en',

    'brand' => [
        'name' => 'ProcureAI',
        'tagline' => 'AI Procurement Negotiation Assistant',
    ],

    'industries' => [
        'manufacturing',
        'retail',
        'restaurant',
        'construction',
        'healthcare',
        'agency',
        'ecommerce',
        'government',
        'custom',
    ],

    'demo_accounts' => [
        [
            'role' => 'Platform Super Admin',
            'email' => 'superadmin@procureai.test',
            'password_hint' => 'password',
            'login_url' => '/admin/login',
            'description' => 'Platform panel (/admin); no tenant. Full matrix in docs/DEMO_ACCOUNTS.md.',
        ],
        [
            'role' => 'Acme Manufacturing Owner',
            'email' => 'owner@acme-manufacturing.test',
            'password_hint' => 'password',
            'login_url' => '/app/login',
            'description' => 'Enterprise tenant with nearly all modules enabled.',
        ],
        [
            'role' => 'Urban Retail Buyer',
            'email' => 'buyer@urban-retail-co.test',
            'password_hint' => 'password',
            'login_url' => '/app/login',
            'description' => 'Starter tenant lean sourcing + AI negotiation.',
        ],
        [
            'role' => 'BuildRight Warehouse',
            'email' => 'warehouse@buildright-construction.test',
            'password_hint' => 'password',
            'login_url' => '/app/login',
            'description' => 'Pro tenant P2P / GRN / inventory focus.',
        ],
        [
            'role' => 'Supplier Portal (Acme)',
            'email' => 'supplier1@acme-manufacturing.test',
            'password_hint' => 'password',
            'login_url' => '/supplier/login',
            'description' => 'Respond to RFQs and submit quotes on the supplier panel.',
        ],
    ],

    'pricing' => [
        'currencies' => [
            'USD' => ['symbol' => '$', 'rate' => 1],
            'EUR' => ['symbol' => '€', 'rate' => 0.92],
            'GBP' => ['symbol' => '£', 'rate' => 0.79],
            'BDT' => ['symbol' => '৳', 'rate' => 110],
        ],
        'plans' => [
            'starter' => [
                'name' => 'Starter',
                'monthly' => 299,
                'yearly' => 2990,
                'modules' => ['requisitions', 'rfq', 'quotes', 'suppliers', 'catalog'],
            ],
            'growth' => [
                'name' => 'Growth',
                'monthly' => 699,
                'yearly' => 6990,
                'modules' => ['quote_comparison', 'ai_negotiation', 'approvals', 'purchase_orders', 'savings'],
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'monthly' => 1499,
                'yearly' => 14990,
                'modules' => ['all'],
            ],
        ],
    ],

    'quote_comparison_demo' => [
        'line_items' => [
            ['sku' => 'STL-BOLT-M12', 'description' => 'M12 hex bolts (box of 500)'],
            ['sku' => 'PPE-GLOVE-L', 'description' => 'Cut-resistant gloves, large'],
            ['sku' => 'LUB-IND-20L', 'description' => 'Industrial lubricant 20L drum'],
        ],
        'suppliers' => ['Demo Industrial Supply', 'Metro Parts Co.', 'Harbor Logistics'],
        'matrix' => [
            [1240.00, 1185.50, 1310.00],
            [89.00, 92.50, 85.25],
            [410.00, 398.00, 425.75],
        ],
    ],

    'contact' => [
        'to' => env('MAIL_FROM_ADDRESS', 'hello@procureai.test'),
    ],
];
