<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Pricing;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Rule
 */
class Rule extends AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_pricing_rule',
            'id'
        );
    }
}
