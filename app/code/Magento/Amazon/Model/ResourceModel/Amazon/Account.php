<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Model\ResourceModel\Amazon;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Account
 */
class Account extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_account',
            'merchant_id'
        );
    }
}
