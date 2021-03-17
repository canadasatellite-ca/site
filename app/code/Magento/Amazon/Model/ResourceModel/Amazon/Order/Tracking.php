<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Order;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Tracking
 */
class Tracking extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_order_tracking',
            'id'
        );
    }
}
