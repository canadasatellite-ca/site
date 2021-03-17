<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

use Magento\Amazon\Model\Amazon\Definitions;

$data = [
    [
        'uuid' => 'authentication_pending_account',
        'name' => 'My Authentication Pending Test Account',
        'email' => 'authentication-pending-account@example.com',
        'base_url' => 'https://example.com/authentication-pending',
        'is_active' => Definitions::ACCOUNT_STATUS_INCOMPLETE,
        'country_code' => 'US'
    ],
    [
        'uuid' => 'inactive_account',
        'name' => 'My Inactive Test Account',
        'email' => 'inactive-account@example.com',
        'base_url' => 'https://example.com/inactive',
        'is_active' => Definitions::ACCOUNT_STATUS_INACTIVE,
        'country_code' => 'GB'
    ],
    [
        'uuid' => 'incomplete_account',
        'name' => 'My Incomplete Test Account',
        'email' => 'incomplete-account@example.com',
        'base_url' => 'https://example.com/incomplete',
        'is_active' => Definitions::ACCOUNT_STATUS_INCOMPLETE,
        'country_code' => 'CA'
    ],
    [
        'uuid' => 'active_account',
        'name' => 'My Active Test Account',
        'email' => 'active-account@example.com',
        'base_url' => 'https://example.com/active',
        'is_active' => Definitions::ACCOUNT_STATUS_ACTIVE,
        'country_code' => 'MX'
    ]
];

return array_combine(array_column($data, 'uuid'), $data);
